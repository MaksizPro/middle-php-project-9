<?php

declare(strict_types=1);

use Slim\App;

return function (App $app) {
    //slim/flash
    $app->add(
        function ($request, $next) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            return $next->handle($request);
        }
    );
};
