<?php

namespace App\Client\Mail;

use App\Entity\Region;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NcziMorningEmailClient extends \App\Client\AbstractClient
{
    const SPF_PASS_PATTERN = '/^authentication-results: spf=pass(\n|.)*?smtp\.mailfrom=nczisk\.sk(\n|.)*?compauth=pass/im';
    const ATTRIBUTE_PATTERNS = [
        // date since when the attributes were available in the email
        '2020-01-01' => [
            // regional_tests.positives_pcr attribute is expanded to two attributes per region:
            //      region_<REGION-ABBREVIATION>_tests_pcr_positive_total
            //      region_<REGION-ABBREVIATION>_tests_pcr_positive_delta
            'REGIONAL_PCR' => '\|(?<v1>[^\| ]+?) kraj\|(?<v2>\d+)(\|(?<v3>\d+))?',

            'slovakia_tests_pcr_positive_delta' => 'pribud(li|ol|lo) (?<v1>\d+|žiadny|žiaden) .*pozitívne testovan(ých|í|ý) (pacient|pacienti|pacientov|osôb)(\.| PCR)',
            'hospital_beds_occupied_jis_covid' => 'na JIS *(?<v1>\d+)',
            'hospital_patients_confirmed_covid' => 'potvrdeným covid19 *(?<v1>\d+)',
            'hospital_patients_suspected_covid' => 'podozrením na covid19 *(?<v1>\d+)',
            'hospital_patients_ventilated_covid' => 'na pľúcnej ventilácii *(?<v1>\d+)',
            'hospital_patients_all_covid' => 'Celkový počet hospitalizovaných pacientov (?<v1>\d+)',
        ],
        '2020-06-22' => [
            'slovakia_tests_pcr_positive_delta_without_quarantine' => 'mimo karanténnych centier(|\/e\-Karantény) (?<v1>\d+)[^\d]',
        ],
        '2020-11-19' => [
            'slovakia_tests_ag_all_total' => [
                'Ag testov poskytovateľmi zdravotnej starostlivosti [^sz].*?(?<v1>\d+)',
                'Ag testov poskytovateľmi zdravotnej starostlivosti\|(?<v1>\d+)',
            ],
            'slovakia_tests_ag_all_delta' => 'Ag testov poskytovateľmi zdravotnej starostlivosti za.*?(?<v1>\d+)',
            'slovakia_tests_ag_positive_total' => [
                'Ag testov poskytovateľmi zdravotnej starostlivosti s pozitívnym výsledkom [^z].*?(?<v1>\d+)',
                'Ag testov poskytovateľmi zdravotnej starostlivosti s pozitívnym výsledkom\|(?<v1>\d+)',
            ],
            'slovakia_tests_ag_positive_delta' => 'Ag testov poskytovateľmi zdravotnej starostlivosti s pozitívnym výsledkom za.*?(?<v1>\d+)',
        ],
        '2021-01-14:2021-02-07' => [
            'slovakia_vaccination_all_total' => 'zaočkovaných osôb poskytovateľmi zdravotnej starostlivosti (?<v1>\d+)',
            'slovakia_vaccination_all_delta' => [
                'zaočkovaných osôb poskytovateľmi zdravotnej starostlivosti od.*?(?<v1>\d+)',
                'zaočkovaných osôb poskytovateľmi zdravotnej starostlivosti za predchádzajúci deň (?<v1>\d+)',
            ],
        ],
    ];

    private $debug = false;
    private $regionRepository;
    private $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->regionRepository = $entityManager->getRepository(Region::class);
        $this->parameterBag = $parameterBag;
    }

    protected function hydrateItem(array $item): array
    {
        return $item;
    }

    /**
     * @param array $emails
     * @return array[]
     */
    public function resolvedEmailsAndErrors(array $emails): array
    {
        $errors = [];
        $notices = [];
        $regions = [];

        foreach ($this->regionRepository->findAll() as $region) {
            $regions[$region->getTitle()] = $region;
        }

        $emailsWithResolvedDates = [];

        // first pass - validate sender and resolve dates
        foreach ($emails as $index => $email) {
            try {
                if ($this->isValidSender($email)) {
                    $email['reported_at'] = $this->datetimeFromEmailDate($email['headers']['date']);

                    try {
                        $email['published_on'] = $this->datetimeFromEmailSubject($email['headers']['subject'], $email['reported_at']);
                    } catch (Exception $exception) {
                        $email['published_on'] = $email['reported_at']->sub(new DateInterval('P1D'));
                        $this->updateErrors($errors, $email, $exception->getMessage());
                        $this->updateErrors($errors, $email, 'NOTICE: published_on was calculated as one day before reported_at time ' . $email['headers']['date'] . '. You can ignore previous error.');
                    }

                    $emailsWithResolvedDates[] = $email;
                }
            } catch (Exception $exception) {
                $this->updateErrors($errors, $email, $exception->getMessage());
            }
        }

        // second pass - sort by date
        usort($emailsWithResolvedDates, function ($a, $b) {
            return $a['published_on'] > $b['published_on'] ? 1 : ($a['published_on'] < $b['published_on'] ? -1 : 0);
        });

        $emailWithResolvedAttributes = [];

        // third pass - resolve attributes
        foreach ($emailsWithResolvedDates as $email) {
            try {
                $day = $email['published_on']->format('Y-m-d');

                if (!isset($emailWithResolvedAttributes[$day])) {
                    $emailWithResolvedAttributes[$day] = [
                        'published_on' => $email['published_on'],
                        'reported_at' => $email['reported_at'],
                        'attributes' => null,
                        'content' => $email['content'],
                    ];
                }

                $previousAttributes = $emailWithResolvedAttributes[$day]['attributes'];

                // In case of multiple emails from the same date (day) data from the emails are merged (from earliest to latest email).
                // For example: when a correction of (some) data is sent in the later emails the data should be merged correctly.
                list($attributes, $contentErrors) = $this->statsFromEmailContent($email['content'], $email['published_on'], $previousAttributes, $regions);
                $emailWithResolvedAttributes[$day]['attributes'] = $attributes;

                if (!empty($contentErrors)) {
                    foreach ($contentErrors as $contentError) {
                        $this->updateErrors($errors, $email, $contentError);
                    }
                }
            } catch (Exception $exception) {
                $this->updateErrors($errors, $email, $exception->getMessage());
            }
        }

        $resolvedEmails = [];

        // fourth pass - remove unnecessary errors
        foreach ($emailWithResolvedAttributes as $key => $email) {
            $date = DateTimeImmutable::createFromFormat('Y-m-d', $key);
            $allAttributesRetrieved = true;

            foreach ($this->validAttributePatterns($date) as $attributeName => $pattern) {
                if (!isset($email['attributes'][$attributeName]) ||
                    ($attributeName === 'REGIONAL_PCR' && 8 !== count($email['attributes'][$attributeName]))) {
                    $allAttributesRetrieved = false;
                    break;
                }
            }

            if (isset($errors[$key]) && $allAttributesRetrieved) {
                $notices[$key] = $errors[$key];
                $notices[$key][] = 'Previous email was successfully processed. You can (probably) ignore error(s) above.';

                unset($errors[$key]);
            }
        }

        // fifth pass - expanding REGIONAL_PCR attribute
        foreach ($emailWithResolvedAttributes as $key => $email) {
            if (null === $email['attributes']) {
                $email['attributes'] = [];
            } else {
                if (isset($email['attributes']['REGIONAL_PCR'])) {
                    foreach ($email['attributes']['REGIONAL_PCR'] as $regionalData) {
                        $abbr = $regionalData['region']->getAbbreviation();
                        $email['attributes']["region_{$abbr}_tests_pcr_positive_total"] = $regionalData['total'];
                        $email['attributes']["region_{$abbr}_tests_pcr_positive_delta"] = $regionalData['delta'];
                    }

                    unset($email['attributes']['REGIONAL_PCR']);
                }
            }

            $resolvedEmails[$key] = $email;
        }

        return [$resolvedEmails, $errors, $notices];
    }

    private function isValidSender(array $email)
    {
        if (!preg_match(self::SPF_PASS_PATTERN, $email['raw_headers'])) {
            return false;
        }

        $validSenders = explode(',', $this->parameterBag->get('korona_email_valid_senders'));

        foreach ($email['headers']['sender'] as $sender) {
            if (in_array($sender['mailbox'] . '@' . $sender['host'], $validSenders)) {
                return true;
            }
        }

        return false;
    }

    private function datetimeFromEmailDate(string $emailDate): DateTimeImmutable
    {
        $result = DateTimeImmutable::createFromFormat('D, j M Y H:i:s O', $emailDate)->setTimezone(new DateTimeZone('Europe/Bratislava'));

        if (false === $result) {
            $this->throwException('Date string could not be converted do DateTimeImmutable. Date=' . $emailDate);
        }

        return $result;
    }

    private function datetimeFromEmailSubject(string $emailSubject, DateTimeImmutable $reportedAt): DateTimeImmutable
    {
        $dateString = preg_replace('/ /', '', $emailSubject);
        preg_match('/(\d+\.\d+\.\d+)/', $dateString, $matches);
        $dateString = isset($matches[0]) ? trim($matches[0]) : '';

        $result = DateTimeImmutable::createFromFormat('j.n.Y', $dateString, new DateTimeZone('Europe/Bratislava'));

        if (false === $result) {
            $dateString = preg_replace('/ /', '', $emailSubject);
            preg_match('/(\d+\.\d+\.)/', $dateString, $matches);
            $dateString = isset($matches[0]) ? trim($matches[0]) : '';

            $result = DateTimeImmutable::createFromFormat('j.n.Y', $dateString . $reportedAt->format('Y'), new DateTimeZone('Europe/Bratislava'));

            if (false === $result) {
                $this->throwException('Date could not be extracted from subject. Subject=' . $emailSubject);
            }
        }

        return $result->setTime(0, 0, 0);
    }

    /**
     * @param string $emailContent
     * @param DateTimeImmutable $publishedOn
     * @param array|null $previousAttributes
     * @param array $regions
     * @return array
     * @throws Exception
     */
    private function statsFromEmailContent(string $emailContent, DateTimeImmutable $publishedOn, ?array $previousAttributes = null, array $regions): array
    {
        $htmlContent = preg_replace('/[\r\n \t]+/', " ", $emailContent);
        $htmlContent = preg_replace('/ ?(\| )+/', "|", $htmlContent);
        $htmlContent = preg_replace('/Z dôvodu zmeny zdroja.*poskytovateľov zdravotnej starostlivosti/', "", $htmlContent);
        $htmlContent = preg_replace('/(\d) (\d)/', "$1$2", $htmlContent);
        $htmlContent = preg_replace('/: *(\d+)/', " $1.", $htmlContent);

        $separator = 'Ranné štatistiky k ' . $publishedOn->format('j.n.Y');

        list($htmlContent) = explode($separator, $htmlContent);

        $attributes = [];
        $validAttributePatterns = $this->validAttributePatterns($publishedOn);

        foreach ($validAttributePatterns as $attribute => $patterns) {
            $patterns = is_array($patterns) ? $patterns : [$patterns];

            foreach ($patterns as $pattern) {
                if (preg_match_all("/$pattern/", $htmlContent, $matches)) {
                    if (!isset($attributes[$attribute])) {
                        $attributes[$attribute] = [];
                    }

                    for ($f = 1; $f < 9; $f++) {
                        $valueName = "v$f";

                        if (isset($matches[$valueName])) {
                            foreach ($matches[$valueName] as $matchIndex => $value) {
                                $attributes[$attribute][$matchIndex][$valueName] = $value;
                            }
                        }
                    }
                }
            }
        }

        $failOnMissingAttributes = null === $previousAttributes;
        $result = null === $previousAttributes ? [] : $previousAttributes;
        $errors = [];

        foreach ($validAttributePatterns as $attribute => $pattern) {
            if (!isset($attributes[$attribute])) {
                $errors[] = 'Attribute could not be extracted from content. Attribute=' . $attribute;
                continue 1;
            }

            if ('REGIONAL_PCR' === $attribute) {
                if ($failOnMissingAttributes && 8 !== count($attributes[$attribute])) {
                    $errors[] = 'Not enough regions (should be 8 not ' . count($attributes[$attribute]) . '). Attribute=' . $attribute;
                }

                foreach ($attributes[$attribute] as $index => $regionAttributes) {
                    if (2 > count($regionAttributes)) {
                        $errors[] = 'Not enough region attributes (should be 2 or 3 not ' . count($regionAttributes) . '). RegionIndex=' . $index;
                        continue 1;
                    }

                    if (!isset($regions[$regionAttributes['v1']])) {
                        $errors[] = 'Unknown region. Region=' . $regionAttributes['v1'];
                        continue 1;
                    }

                    if (empty($regionAttributes['v2'])) {
                        $errors[] = 'Total positives should not be zero. Region=' . $regionAttributes['v1'];
                        continue 1;
                    }

                    if (!isset($regionAttributes['v3'])) {
                        $regionAttributes['v3'] = 0;
                    }

                    if ((int)$regionAttributes['v3'] > (int)$regionAttributes['v2']) {
                        $errors[] = 'Total positives should larger than delta positives. Region=' . $regionAttributes['v1'];
                        continue 1;
                    }

                    $result[$attribute][$regions[$regionAttributes['v1']]->getId()] = [
                        'region' => $regions[$regionAttributes['v1']],
                        'total' => (int)$regionAttributes['v2'],
                        'delta' => (int)$regionAttributes['v3'],
                    ];
                }
            } else {
                $valuesCount = count($attributes[$attribute]);

                if (1 !== $valuesCount) {
                    $values = [];

                    foreach ($attributes[$attribute] as $attributeAttribute) {
                        $values[] = isset($attributeAttribute['v1']) ? $attributeAttribute['v1'] : 'NA';
                    }

                    if ($failOnMissingAttributes && $valuesCount === 0) {
                        $errors[] = 'Zero values (should be exactly one). Attribute=' . $attribute . '. Values=' . implode(', ', $values);
                        continue 1;
                    }

                    if ($valuesCount > 1) {

                        $errors[] = 'Multiple attribute values (should be exactly one). Attribute=' . $attribute . '. Values=' . implode(', ', $values);
                        continue 1;
                    }
                } else {
                    if (mb_strpos($attributes[$attribute][0]['v1'], 'žiad') === 0) {
                        $result[$attribute] = 0;
                    } else {
                        $result[$attribute] = (int)$attributes[$attribute][0]['v1'];
                    }
                }
            }
        }

        return [$result, $errors];
    }

    private function validAttributePatterns(DateTimeImmutable $publishedOn)
    {
        $result = [];

        $publishedOnFormatted = $publishedOn->format('Y-m-d');

        foreach (self::ATTRIBUTE_PATTERNS as $validInterval => $attributePatterns) {
            $validIntervalParts = explode(':', $validInterval);
            $validSince = $validIntervalParts[0];
            $validUntil = $validIntervalParts[0] ?? null;

            if ($validSince <= $publishedOnFormatted && (null === $validUntil || $publishedOnFormatted <= $validUntil)) {
                $result += $attributePatterns;
            }
        }

        return $result;
    }

    public function emails(): array
    {
        $formData = [
            'mailbox' => $this->parameterBag->get('korona_email_mailbox'),
            'user' => $this->parameterBag->get('korona_email_user'),
            'password' => $this->parameterBag->get('korona_email_password'),
            'token' => $this->parameterBag->get('korona_email_proxy_token'),
            'subject' => 'Rann',
            'since' => (new DateTimeImmutable('2 days ago'))->format('j F Y'),
        ];

        if ($this->debug) {
            $filename = 'mailbox-' . md5(json_encode($formData)) . '.json';

            if (!is_file($filename)) {
                file_put_contents($filename, $this->content->load($this->parameterBag->get('korona_email_proxy_url'), ['form' => $formData]));
            }

            $mailbox = json_decode($this->content->load($filename), true);
        } else {
            $mailbox = json_decode($this->content->load($this->parameterBag->get('korona_email_proxy_url'), ['form' => $formData]), true);
        }

        if (!is_array($mailbox)) {
            $this->throwException('Could not retrieve messages from mailbox.');
        }

        if (!isset($mailbox['success']) || false === $mailbox['success']) {
            $this->throwException($mailbox['error']);
        }

        if (!isset($mailbox['messages'])) {
            $this->throwException('Malformed response');
        }

        return $mailbox['messages'];
    }

    private function throwException($message)
    {
        throw new Exception($message);
    }

    private function updateErrors(&$errors, array $email, string $message)
    {
        if (isset($email['published_on'])) {
            $date = $email['published_on']->format('Y-m-d');
        } else {
            $date = $email['headers']['date'];
        }

        $errors[$date] = isset($errors[$date]) ? $errors[$date] : [];
        $errors[$date][] = '[' . $email['headers']['date'] . '] ' . $message;
    }

    protected function dataItemToEntities(array $dataItemItem): array
    {
        // TODO: Implement dataItemToEntities() method.
    }
}