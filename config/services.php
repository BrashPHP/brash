<?php

declare(strict_types=1);
use Brash\Framework\Cli\Services\PlatesService;
use Brash\Framework\Cli\Services\TermwindService;

return [
    /****************************************************************************
     * Application Services
     * --------------------------------------------------------------------------
     *
     * The services to be loaded for your application.
     *****************************************************************************/

    'services' => [
        'termwind' => TermwindService::class,
        'plates' => PlatesService::class,
    ],
];
