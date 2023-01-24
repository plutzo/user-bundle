<?php

declare(strict_types=1);


use Marlinc\UserBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

$application = new Application(new AppKernel());
$application->setAutoExit(false);

$input = new ArrayInput([
    'command' => 'doctrine:database:drop',
    '--force' => true,
]);
$application->run($input, new NullOutput());

$input = new ArrayInput([
    'command' => 'doctrine:database:create',
    '--no-interaction' => true,
]);
$application->run($input, new NullOutput());

$input = new ArrayInput([
    'command' => 'doctrine:schema:create',
]);
$application->run($input, new NullOutput());
