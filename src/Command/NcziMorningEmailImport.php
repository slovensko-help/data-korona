<?php

namespace App\Command;

use App\Client\Mail\NcziMorningEmailClient;
use App\Entity\Raw\NcziMorningEmail;
use App\Entity\Notification;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PropertyAccess\PropertyAccess;

class NcziMorningEmailImport extends AbstractImport
{
    protected static $defaultName = 'app:import:nczi-morning-email';

    /** @var NcziMorningEmailClient */
    private $ncziMorningEmailClient;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disableDoctrineLogger();

        $output->writeln($this->log('Fetching emails...'));
        $emails = $this->ncziMorningEmailClient->emails();
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating db...'));

        list($resolvedEmails, $errors, $notices) = $this->ncziMorningEmailClient->resolvedEmailsAndErrors($emails);

        foreach ($resolvedEmails as $record) {
            $email = $this->ncziMornignEmail($record);
            $emails[] = $email;
        }

        $this->notifyErrors($errors);

        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    protected function ncziMornignEmail(array $record): ?NcziMorningEmail
    {
        $id = (int)$record['published_on']->format('Ymd');

        return $this->updateOrCreate(function (?NcziMorningEmail $ncziMorningEmail) use ($record, $id) {
            $ncziMorningEmail = ($ncziMorningEmail ?? new NcziMorningEmail());

            if (!$ncziMorningEmail->isManuallyOverridden()) {
                $ncziMorningEmail
                    ->setId($id)
                    ->setPublishedOn($record['published_on'])
                    ->setReportedAt($record['reported_at']);

                $propertyAccessor = PropertyAccess::createPropertyAccessor();

                foreach ($record['attributes'] as $attributeName => $attributeValue) {
                    $propertyAccessor->setValue($ncziMorningEmail, $attributeName, $attributeValue);
                }
            }

            return $ncziMorningEmail;
        }, $this->entityManager->getRepository(NcziMorningEmail::class), ['id' => $id], true);
    }

    private function notifyErrors(array $errors)
    {
        $errorsText = [];
        foreach ($errors as $date => $errorsForDate) {
            if (!$this->wasNotified(self::$defaultName . 'errors-on' . $date, $errorsForDate)) {
                $errorsText[] = "Email zo dňa $date:";
                $errorsText[] = "------------------------";

                foreach ($errorsForDate as $error) {
                    $errorsText[] = $error;
                }

                $errorsText[] = "";
            }
        }

        if (!empty($errorsText)) {
            $text = "Zdravím\n\n";
            $text .= "je nutné skontrolovať email(y) z NCZI, pretože pri automatickom extrahovaní údajov došlo k nejakým chybám.\n\n";
            $text .= implode("\n", $errorsText);
            $text .= "\n\nS pozdravom,\n";
            $text .= "Automat na data.korona.gov.sk\n";

            $email = (new Email())
                ->from('data.korona.gov.sk <filip@bratia.sk>')
                ->to('korona.gov@krizovystab.sk')
                ->subject('❗ Konverzia NCZI emailu do API si vyžaduje hýčkanie a opateru')
                ->text($text);

            $this->mailer->send($email);
        }
    }

    private function wasNotified($rawObjectId, $content)
    {
        $id = md5($rawObjectId);

        $entities = $this->updateOrCreate(function (?Notification $ncziMorningEmail) use ($id, $content) {
            return ($ncziMorningEmail ?? new Notification())
                ->setId($id)
                ->setContentHash(md5(json_encode($content)))
                ->setContent(json_encode($content));

        }, $this->entityManager->getRepository(Notification::class), ['id' => $id], true, true);

        /** @var Notification $beforeUpdate */
        $beforeUpdate = $entities['before'];

        /** @var Notification $afterUpdate */
        $afterUpdate = $entities['after'];

        return null !== $beforeUpdate && $beforeUpdate->getContentHash() === $afterUpdate->getContentHash();
    }

    /**
     * @required
     * @param NcziMorningEmailClient $ncziMorningEmailClient
     */
    public function setNcziMorningEmailClient(NcziMorningEmailClient $ncziMorningEmailClient): void
    {
        $this->ncziMorningEmailClient = $ncziMorningEmailClient;
    }
}