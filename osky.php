<?php

require __DIR__ . '/autoload.php';

use Osky\Command\RedditSearchCmd;

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new RedditSearchCmd());
$application->run();