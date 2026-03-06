<?php

use Rector\Config\RectorConfig;
use SavinMikhail\AddNamedArgumentsRector\AddNamedArgumentsRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app/Product',
        __DIR__ . '/app/Auth',
    ])
    ->withRules([
        AddNamedArgumentsRector::class,
    ])
    ->withCache(__DIR__ . '/var/rector');