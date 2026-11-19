<?php

declare(strict_types=1);

use App\Components\Helpers;
use App\Models\Url;
use App\Models\UrlCheck;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteContext;
use Slim\Views\PhpRenderer;
use Symfony\Component\DomCrawler\Crawler;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $renderer = new PhpRenderer('../templates', ['title' => 'Главная']);
        $renderer->setLayout('layouts/app.php');
        return $renderer->render($response, 'index.php');
    });

    $app->post('/', function (Request $request, Response $response) {

        $formData = $request->getParsedBody();
        $v = new Valitron\Validator(['url' => $formData['url']]);

        $v->rule('required', 'url')->message('{field} не должен быть пустым');
        $v->rule('lengthMax', 'url', 255)->message('{field} превышает 255 символов');
        $v->rule('regex', 'url', '/^(?:h+t{2,}p+(s+)?:+\/+).*\..*$/')->message('Некорректный {field}');

        $templatesData = [
            'title' => 'Главная'
        ];

        if (!$v->validate()) {
            $templatesData['errors'] = $v->errors()['url'];
            $renderer = new PhpRenderer('../templates', $templatesData);
            $renderer->setLayout('layouts/app.php');

            return $renderer->render($response, 'index.php', [
                'url' => $formData['url'],
            ])->withStatus(422);
        }

        $parsedUrl = parse_url($formData['url']);
        $normalizedDomain = $parsedUrl['host'];
        $normalizedScheme = 'http';
        if (preg_match('/h+t{2,}p+s+/', $parsedUrl['scheme'])) {
            $normalizedScheme = 'https';
        }
        if (isset($parsedUrl['user'])) {
            $normalizedDomain = $parsedUrl['user'] . '@' . $normalizedDomain;
        }

        $formDataUrlCleared = "{$normalizedScheme}://{$normalizedDomain}";
        $url = Url::where('name', $formDataUrlCleared)->first();

        if (!$url) {
            $url = new Url();
            $url->name = $formDataUrlCleared;
            $url->save();
            $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
        } else {
            $this->get('flash')->addMessage('success', 'Страница уже существует');
        }

        $url = RouteContext::fromRequest($request)->getRouteParser()->urlFor('urls.show', ['id' => $url->id]);
        return $response->withStatus(302)->withHeader('Location', $url);
    })->setName('urls.store');

    $app->get('/urls/{id}', function (Request $request, Response $response, array $args) {

        $url = Url::find($args['id']);
        $url->checks = UrlCheck::where('url_id', $url->id)->get();
        $messages = $this->get('flash')->getMessages();

        $templatesData = [
            'title' => 'Главная'
        ];

        if (isset($messages['success']) && count($messages['success'])) {
            $templatesData['success'] = $messages['success'];
        }
        if (isset($messages['errors']) && count($messages['errors'])) {
            $templatesData['errors'] = $messages['errors'];
        }

        $renderer = new PhpRenderer('../templates', $templatesData);
        $renderer->setLayout('layouts/app.php');

        //dd(Helpers::trimLongTextInArray($url->checks, ['h1', 'title', 'description']));

        return $renderer->render($response, 'urls-show.php', [
            'id' => $url->id,
            'url' => $url->name,
            'created_at' => $url->created_at,
            'checks' => Helpers::trimLongTextInArray($url->checks, ['h1', 'title', 'description'])
        ]);
    })->setName('urls.show');

    $app->get('/urls', function (Request $request, Response $response) {

        $urls = Url::get();
        $urlsIds = [];

        foreach ($urls as $value) {
            $urlsIds[] = $value->id;
        }

        $urlsChecks = UrlCheck::whereInOne('url_id', $urlsIds)->get();

        foreach ($urls as $url) {
            foreach ($urlsChecks as $urlCheck) {
                if ($urlCheck->url_id == $url->id && !$url->lastCheck) {
                    $url->lastCheck = $urlCheck;
                }
            }
        }

        $templatesData = [
            'title' => 'Сайты'
        ];

        $renderer = new PhpRenderer('../templates', $templatesData);
        $renderer->setLayout('layouts/app.php');

        return $renderer->render($response, 'urls-index.php', [
            'urls' => $urls
        ]);
    })->setName('urls.index');

    $app->post('/urls/{id}/checks', function (Request $request, Response $response, array $args) {

        $url = Url::find($args['id']);
        $client = new GuzzleHttp\Client();

        try {
            $res = $client->request('GET', $url->name, [
                'auth' => ['user', 'pass']
            ]);

            $crawler = new Crawler($res->getBody()->getContents());

            $check = new UrlCheck();
            $check->url_id = $url->id;
            $check->status_code = $res->getStatusCode();
            $check->h1 = $crawler->filter('h1')->extract(['_text'])[0] ?? null;
            $check->title = $crawler->filter('title')->extract(['_text'])[0] ?? null;
            $check->description = $crawler->filter('meta[name="description"]')->extract(['content'])[0] ?? null;
            $check->save();

            $this->get('flash')->addMessage('success', 'Страница успешно проверена');
        } catch (\Throwable $th) {
            $this->get('flash')->addMessage('errors', 'Произошла ошибка при проверке, не удалось подключиться');
        }

        $urlResponse = RouteContext::fromRequest($request)->getRouteParser()->urlFor('urls.show', ['id' => $url->id]);
        return $response->withStatus(302)->withHeader('Location', $urlResponse);
    })->setName('urls.checks.store');
};
