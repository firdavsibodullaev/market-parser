<?php

namespace App\Parser\Subjects;

use App\Constants\TypeShopConstant;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class Verniy extends Subject
{
    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url . '/contacts';
        $this->brand = 'Верный';
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $html = file_get_contents($this->url);
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $filter = $crawler
            ->filter('.our-offices-wrap>.our-offices__inner')->reduce(function (Crawler $node, $i) {
                return $i < 4;
            });

        return $this->getParsedData($filter);
    }

    /**
     * @param Crawler $filter
     * @return array
     */
    protected function getParsedData(Crawler $filter): array
    {

        $count = $filter->count();

        $data = [];

        for ($i = 0; $i < $count; $i++) {
            $place = $filter->eq($i)->filter('.our-offices-place')->first()->text();
            $address = $filter->eq($i)->filter('.our-offices-adress')->first()->text();

            list($post, $city) = explode(', г.', $place);
            list($address, $phone) = explode(' +', $address);

            $data[] = [
                'namebrand' => $this->brand,
                'post' => $post,
                'uin' => Str::uuid(),
                'region' => null,
                'city' => $this->clearSpaces($city),
                'street' => $this->clearSpaces($address),
                'typeshop' => TypeShopConstant::SUPERMARKET,
                'tc' => preg_match('/ТЦ.|Торговый центр|Торговый Центр|торговый центр/', $address) ? 'Да' : 'Нет'
            ];
        }

        return $data;
    }
}
