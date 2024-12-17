<?php

declare(strict_types=1);

use Rector\CodingStyle;
use Rector\Config\RectorConfig;
use Rector\Privatization;

return RectorConfig::configure()
    ->withPhpSets(php82: true)
    ->withPaths([
        __DIR__ . '/src',
    ]);