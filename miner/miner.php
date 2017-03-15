#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Yaml\Yaml;


$application = new Application();

$application->add(new class extends Command {
    private $path;

    private static $jsonFormat = <<<'FORMAT'
{%n  "commit": "%H",%n  "abbreviated_commit": "%h",%n  "tree": "%T",%n  "abbreviated_tree": "%t",%n  "parent": "%P",%n  "abbreviated_parent": "%p",%n  "refs": "%D",%n  "encoding": "%e",%n  "subject": "%s",%n  "sanitized_subject_line": "%f",%n  "body": "%b",%n  "commit_notes": "%N",%n  "verification_flag": "%G?",%n  "signer": "%GS",%n  "signer_key": "%GK",%n  "author": {%n    "name": "%aN",%n    "email": "%aE",%n    "date": "%ad"%n  },%n  "commiter": {%n    "name": "%cN",%n    "email": "%cE",%n    "date": "%cd"%n  }%n}
FORMAT;


    private function gitCommand($command) {
        if (!is_array($command)) $command = [$command];
        ksort($command);

        array_unshift($command, 'git');

        $process = (new ProcessBuilder($command))
            ->setWorkingDirectory($this->path)
            ->getProcess();

        $process->run();
        return $process->getOutput();
    }

    protected function configure()
    {
        $this->setName('mine')
            ->addArgument('repository', InputArgument::REQUIRED, "The path to the ece-stats repository");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->path = $input->getArgument('repository');
        $data = Yaml::parse(file_get_contents(__DIR__ . '/../_data/notes.yml'));

        $output = [];
        foreach ($data as $subject) {
            $filename = $subject['slug'] . '.tex';

            $arguments = [
                'log',
                '-n 1',
                '--date=iso-strict',
                '--format=' . self::$jsonFormat,
                '-i',
            ];
            $arguments[100] = $filename;

            $lastcommit = json_decode($this->gitCommand($arguments));

            $arguments[20] = "--grep=lecture \\?[0-9]\\+";
            $lastlecture = json_decode($this->gitCommand($arguments));
            if ($lastlecture) {
                preg_match('/(.*) Lecture ([0-9]+)/i', $lastlecture->subject, $matches);
            }

            $filesize = filesize($this->path . '/' . $filename);

            $output[$subject['slug']] = [
                'last_update' => $lastcommit->author->date,
                'lectures' => (!$lastlecture) ? null : [ [
                    'date' => $lastlecture->author->date,
                    'professor' => $matches[1],
                    'number' => (int) $matches[2]
                ] ],
                'filesize' => $filesize
            ];
        }

        file_put_contents(__DIR__ . '/../_data/notes-extracted.yml', Yaml::dump($output));
    }
});

$application->setDefaultCommand('mine', true);
$application->run();
