<?php

namespace App\Parser\Subjects;

use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class MariaRa extends Subject
{

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url . '/o-kompanii/kontakty';
        $this->brand = 'Мария Ра';
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $html = file_get_contents($this->url);
        $crawler = new Crawler($html);
        $filter = $crawler
            ->filter('.article__content>table>tbody>tr')
            ->reduce(function (Crawler $node, $i) {
                return preg_match('/г./', $node->text()) && preg_match('/ул./', $node->text());
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
            $node = $filter->eq($i)->text();

            $address = explode('+', $node, 2)[0];
            $address = explode('г.', $address)[1];
            list($city, $address) = explode('ул.', $address);

            $data[] = [
                'namebrand' => $this->brand,
                'post' => null,
                'uin' => Str::uuid(),
                'region' => null,
                'city' => $this->clearSpaces($city),
                'street' => "ул. {$this->clearSpaces($address)}",
                'tc' => preg_match('/ТЦ.|Торговый центр|Торговый Центр|торговый центр/', $address) ? 'Да' : 'Нет',
            ];
        }

        return $data;
    }
}
