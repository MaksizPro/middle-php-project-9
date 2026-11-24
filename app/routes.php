<?php

declare(strict_types=1);

use Slim\App;
use Symfony\Component\DomCrawler\Crawler;

return function (App $app) {
    $app->get('/', [\App\Controllers\HomeController::class, 'index'])->setName('index');
    $app->post('/urls', [\App\Controllers\UrlController::class, 'store'])->setName('urls.store');
    $app->get('/urls/{id:[0-9]+}', [\App\Controllers\UrlController::class, 'show'])->setName('urls.show');
    $app->get('/urls', [\App\Controllers\UrlController::class, 'index'])->setName('urls.index');
    $app->post('/urls/{id:[0-9]+}/checks', [\App\Controllers\UrlCheckController::class, 'store'])->setName('urls.checks.store');
};
