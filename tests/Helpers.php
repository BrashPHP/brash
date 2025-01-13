<?php

declare(strict_types=1);


use Brash\Framework\Cli\Config\TermwindOutputHandler;
use Brash\Framework\Cli\Services\PlatesService;
use Brash\Framework\Cli\Services\TermwindService;
use Minicli\App;
use Symfony\Component\Console\Output\BufferedOutput;

use function Termwind\renderUsing;

function getApp(): App
{
    $app = new App();
    $app->addService('termwind', new TermwindService());
    $app->addService('plates', new PlatesService());
    $app->setOutputHandler(new TermwindOutputHandler());

    return $app;
}

function getOutput(): BufferedOutput
{
    $output = new BufferedOutput();
    renderUsing($output);

    return $output;
}
