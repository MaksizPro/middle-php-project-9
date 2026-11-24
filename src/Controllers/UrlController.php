<?php

namespace App\Controllers;

use App\Components\Helpers;
use App\Controllers\Controller;
use App\Models\Url;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

class UrlController extends Controller
{
    public function index(ServerRequestInterface $request, ResponseInterface $response)
    {
        $urls = Url::with(['lastCheck'])->get();
        $templatesData = [
            'title' => 'Сайты'
        ];

        $renderer = $this->renderer;
        $renderer->setAttributes(array_merge($renderer->getAttributes(), $templatesData));

        return $renderer->render($response, 'urls-index.php', [
            'urls' => $urls
        ]);
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $url = Url::find($args['id']);
        if (!$url) {
            throw new HttpNotFoundException($request, "Пользователь с ID {$args['id']} не найден");
        }
        $url->checks;

        $messages = $this->flash->getMessages();
        $templatesData = [
            'title' => 'Главная'
        ];

        if (isset($messages['success']) && count($messages['success'])) {
            $templatesData['success'] = $messages['success'];
        }
        if (isset($messages['errors']) && count($messages['errors'])) {
            $templatesData['errors'] = $messages['errors'];
        }

        $renderer = $this->renderer;
        $renderer->setAttributes(array_merge($renderer->getAttributes(), $templatesData));

        return $renderer->render($response, 'urls-show.php', [
            'id' => $url->id,
            'url' => $url->name,
            'created_at' => $url->created_at,
            'checks' => Helpers::trimLongTextInArray($url->checks, ['h1', 'title', 'description'])
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response)
    {
        $formData = $request->getParsedBody();
        $v = new \Valitron\Validator(['url' => $formData['url']]);

        $v->rule('required', 'url')->message('{field} не должен быть пустым');
        $v->rule('lengthMax', 'url', 255)->message('{field} превышает 255 символов');
        $v->rule('url', 'url')->message('Некорректный {field}');

        $templatesData = [
            'title' => 'Главная'
        ];

        if (!$v->validate()) {
            $templatesData['errors'] = $v->errors()['url'];
            $renderer = $this->renderer;
            $renderer->setAttributes(array_merge($renderer->getAttributes(), $templatesData));
            return $renderer->render($response, 'index.php', [
                'url' => $formData['url'],
            ])->withStatus(422);
        }

        $parsedUrl = parse_url($formData['url']);
        $formDataUrlCleared = "{$parsedUrl['scheme']}://{$parsedUrl['host']}";

        $url = Url::where('name', $formDataUrlCleared)->first();

        if (!$url) {
            $url = new Url();
            $url->name = $formDataUrlCleared;
            $url->save();
            $this->flash->addMessage('success', 'Страница успешно добавлена');
        } else {
            $this->flash->addMessage('success', 'Страница уже существует');
        }

        $url = RouteContext::fromRequest($request)->getRouteParser()->urlFor('urls.show', ['id' => $url->id]);
        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
