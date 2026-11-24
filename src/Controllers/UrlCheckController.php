<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\Url;
use App\Models\UrlCheck;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Symfony\Component\DomCrawler\Crawler;

class UrlCheckController extends Controller
{
    public function store(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $url = Url::find($args['id']);
        $client = new Client(['timeout' => 5]);

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

            $this->flash->addMessage('success', 'Страница успешно проверена');
        } catch (TransferException $e) {
            $this->flash->addMessage('errors', 'Произошла ошибка при проверке, не удалось подключиться');
        }

        $urlResponse = RouteContext::fromRequest($request)->getRouteParser()->urlFor('urls.show', ['id' => $url->id]);
        return $response->withStatus(302)->withHeader('Location', $urlResponse);
    }
}
