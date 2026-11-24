<?php

namespace App\Controllers;

use Slim\Flash\Messages;
use Slim\Views\PhpRenderer;

class Controller
{
    protected PhpRenderer $renderer;
    protected Messages $flash;

    public function __construct(PhpRenderer $renderer, Messages $flash)
    {
        $this->renderer = $renderer;
        $this->flash = $flash;
    }
}
