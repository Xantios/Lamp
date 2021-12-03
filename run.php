#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Xantios\Lamp\Command\Ls;
use Xantios\Lamp\Command\Setup;

$application = new Application();

$application->add(new Setup);
$application->add(new Ls);

$application->run();