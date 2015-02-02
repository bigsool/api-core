<?php

use Core\Deploy\Command;
use Symfony\Component\Console\Helper;

require_once __DIR__ . '/../vendor/autoload.php';

$cli = new \Symfony\Component\Console\Application('Deployment Command Line Interface');
$cli->setCatchExceptions(true);
$cli->setHelperSet(new Helper\HelperSet([
                                            'dialog' => new Helper\QuestionHelper(),
                                        ]));
$cli->addCommands([
                      new Command\Install(),
                      new Command\Send(),
                      new Command\Deploy(),
                      new Command\CheckRevision(),
                  ]);

$cli->run();