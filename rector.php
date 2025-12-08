<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use RectorLaravel\Set\LaravelSetList;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()->withPaths([
        __DIR__.'/app',
        __DIR__.'/tests',
    ])->withPhpSets(php83: true)->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
    ])->withConfiguredRule(\RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector::class, [
        'dd', 'dump', 'var_dump',
]);
