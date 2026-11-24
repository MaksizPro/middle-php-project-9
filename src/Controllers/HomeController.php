<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response)
    {
        $renderer = $this->renderer;
        return $renderer->render($response, 'index.php', [
            'title' => 'Главная'
        ]);
    }
}
