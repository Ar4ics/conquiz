<?php

namespace App\Http\Controllers;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Storage;
use Symfony\Component\DomCrawler\Crawler;

class CrawlController extends Controller
{
    public function parse() {
        $client = new Client();
        $guzzleClient = new GuzzleClient(array(
            'timeout' => 60,
        ));
        $client->setClient($guzzleClient);

        $client->setHeader('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:45.0) Gecko/20100101 Firefox/45.0');
        $crawler = $client->request(
            'GET',
            'https://baza-otvetov.ru/categories/view/1'
        );
        $res = [];
        for ($i = 0; $i < 15; $i++) {
            $data = $crawler->filter('table.q-list__table > tr.tooltip')->each(function (Crawler $node) {
                $q = $node->filter('td a')->text();
                $answers = trim($node->filter('td div')->text());
                $correct = $node->filter('td')->last()->text();
                $rest = explode(', ', str_replace('Ответы для викторин: ', '', $answers));

                array_unshift($rest, $correct);
                return [
                    'title' => $q,
                    'answers' => $rest
                ];
            });
            $res = array_merge($res, $data);
            $link = $crawler->selectLink('>')->link();
            $crawler = $client->click($link);
        }
        $json_data = json_encode($res, JSON_UNESCAPED_UNICODE);

        Storage::put('questions.json', $json_data);
        return $res;
    }
}
