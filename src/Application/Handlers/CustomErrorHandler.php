<?php

namespace App\Application\Handlers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Views\PhpRenderer;

class CustomErrorHandler extends ErrorHandler
{
    private PhpRenderer $renderer;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        PhpRenderer $renderer,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($callableResolver, $responseFactory, $logger);
        $this->renderer = $renderer;
    }

    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        $statusCode = 500;
        $template = 'errors/index.php';

        if ($exception instanceof \Slim\Exception\HttpNotFoundException) {
            $statusCode = 404;
            $title = '404 — Страница не найдена';
            $message = $exception->getMessage() ?? 'Запрашиваемая страница не найдена.';
        } elseif ($exception instanceof \Slim\Exception\HttpException) {
            $statusCode = $exception->getCode();
            $title = "Ошибка {$statusCode}";
            $message = $exception->getMessage();
        } else {
            $title = '500 — Внутренняя ошибка сервера';
            $message = 'На сервере произошла непредвиденная ошибка.';
        }

        $details = null;
        if ($this->displayErrorDetails) {
            $details = sprintf(
                "%s: %s in %s:%d\n%s",
                get_class($exception),
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );
        }

        $response = $this->responseFactory->createResponse($statusCode);

        return $this->renderer->render($response, $template, [
            'title'      => $title,
            'statusCode' => $statusCode,
            'message'    => $message,
            'details'    => $details,
        ]);
    }
}
