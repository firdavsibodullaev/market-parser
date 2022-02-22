<?php

namespace App\Parser\Subjects;

use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class Dobrotsen extends Subject
{
    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url . '/busy/contacts';
        $this->brand = 'Доброцен';
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        $html = file_get_contents($this->url);
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $filter = $crawler->filter('.office-location');

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
            $node = $filter->eq($i)->text();
            if (preg_match('/Россия|Казахстан|Беларусь/', $node)) {
                if (preg_match('/Офис в Москве/', $node)) {
                    list($unnecessary_text, $country, $city, $address) = explode(',', $node, 4);
                } else {
                    list($country, $city, $address) = explode(',', $node, 3);
                }
            } else {
                list($city, $address) = explode(',', $node, 2);
            }

            $data[] = [
                'namebrand' => $this->brand,
                'uin' => Str::uuid(),
                'post' => null,
                'region' => null,
                'city' => $this->clearSpaces($city),
                'street' => $this->clearSpaces($address),
                'tc' => preg_match('/ТЦ.|Торговый центр|Торговый Центр|торговый центр/', $address) ? 'Да' : 'Нет',
            ];
        }
        return $data;
    }

}
