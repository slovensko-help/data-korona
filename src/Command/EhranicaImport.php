<?php

namespace App\Command;

use App\Entity\Log;
use App\Entity\Region;
use DateTimeImmutable;
use stdClass;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EhranicaImport extends AbstractImport
{
    protected static $defaultName = 'app:import:ehranica';

    protected function configure()
    {
        parent::configure();

        $this->addOption('import-all-log-files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lastLog = $this->lastLog();

        $this->persist(
            $this->records($lastLog, $input->getOption('import-all-log-files')),
            function (stdClass $_) {
                yield 'id' => function (Log $log) use ($_) {
                    $log->channel = $_->channel;
                    $log->level = $_->level;
                    $log->message = $_->message;
                    $log->code = $_->code;

                    if ($log->createdAt ||
                        $log->createdAt > $_->createdAt ||
                        $log->createdAt < $_->createdAt
                    ) {
                        $log->createdAt = $_->createdAt;
                    }

                    $log->id = $log->createdAt->format('YmdHis') . '-' . str_pad((string)$_->timeIndex, 5, '0', STR_PAD_LEFT);

                    return $log;
                };
            },
            null === $lastLog ? null : [
                Log::class => [$lastLog->createdAt->format('YmdHis'), null,],
            ]
        );

        return self::SUCCESS;
    }

    private function files(bool $importAllLogFiles = false)
    {
        $files = [];

        foreach (glob($this->parameters->get('ehranica-log-files-glob')) as $fileName) {
            $files[] = [
                'path' => $fileName,
                'modified_at' => filemtime($fileName),
            ];
        }

        usort($files, function ($f1, $f2) {
            return $f2['modified_at'] - $f1['modified_at'];
        });

        return $importAllLogFiles ? $files : array_slice($files, 0, 2);
    }

    private function lastLog(): ?Log
    {
        $logs = $this->entityManager->getRepository(Log::class)->findBy(
            ['channel' => 'ehranica'],
            ['createdAt' => 'DESC'],
            1,
            0
        );

        return count($logs) > 0 ? $logs[0] : null;
    }

    private function records(?Log $lastLog, bool $importAllLogFiles = false)
    {
        $timeLogCounts = [];

        foreach ($this->files($importAllLogFiles) as $file) {
            $f = fopen($file['path'], 'r');

            $timezone = new \DateTimeZone('Europe/Bratislava');

            while (($line = fgets($f)) !== false) {
                if (false !== strpos($line, '[EHRANICA]')) {
                    // skip duplicate log for one logical event or events from test env
                    if (
                        false === strpos($line, 'response.payload doesn\'t contain vCovid19Pass variable') &&
                        false === strpos($line, 't.mojeezdravie') &&
                        false === strpos($line, 'test-mojeezdravie')
                    ) {
                        if (preg_match('/^\[(\d{2}-[a-zA-Z]{3}-\d{4} \d{2}:\d{2}:\d{2} .*?)\] \[EHRANICA\]\[(.*?)\] (.*)$/', $line, $matches)) {

                            $log = new stdClass();

                            $log->channel = 'ehranica';
                            $log->level = $matches[2];
                            $log->createdAt = DateTimeImmutable::createFromFormat('d-M-Y H:i:s e', $matches[1])->setTimezone($timezone);
                            $log->message = $matches[3];
                            $log->code = null;

                            if (preg_match('/ status="(\d*)"\.?$/', $log->message, $matches)) {
                                $log->message = preg_replace('/ status="\d*"\.$/', '', $log->message);
                                $log->code = $matches[1];
                            }

                            if (null === $lastLog || $log->createdAt >= $lastLog->createdAt) {
                                $timeString = $log->createdAt->format('YmdHis');
                                $timeLogCounts[$timeString] = $timeLogCounts[$timeString] ?? 0;
                                $timeLogCounts[$timeString]++;

                                $log->timeIndex = $timeLogCounts[$timeString];

                                yield $log;
                            }
                        }
                    }
                }
            }
        }
    }
}