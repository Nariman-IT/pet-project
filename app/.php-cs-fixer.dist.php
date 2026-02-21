<?php

use PHPyh\CodingStandard\PhpCsFixerCodingStandard;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/app/Auth')
    ->in(__DIR__ . '/app/Product')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);


$config = (new Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true) 
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');


(new PhpCsFixerCodingStandard())->applyTo($config);

return $config;