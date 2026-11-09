<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Views\PhpRenderer;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {

        $renderer = new PhpRenderer('../templates', ['title' => 'Главная', 'allert' => 'dasdsd']);
        $renderer->setLayout('layouts/app.php');

        return $renderer->render($response, 'index.php', [
            'name' => 'John',
        ]);
    });
};
