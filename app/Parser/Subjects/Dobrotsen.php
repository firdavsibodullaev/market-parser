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
        $this->url = $url . '/about/shops/'; // /busy/contacts  /about/shops/
        $this->brand = 'Доброцен';

        $this->getMapCoordinates();
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

    /**
     * Pattern for preg_match_all in the getMapCoordinates()
     */
    private $pattern2 = "/\d{2,3}\.\d+, \d{2,3}\.\d+|\d{2,3}\.\d+,\d{2,3}\.\d+|'\d{2,3}\.\d+,\d{2,3}\.\d+'|'\d{2,3}\.\d+, \d{2,3}\.\d+'x/";
    private $pattern1 = "/\[\d{4}\] = new ymaps.Placemark\(\[\d{2,3}\.\d+, \d{2,3}\.\d+\]|\[\d{2,3}\.\d+,\d{2,3}\.\d+\]|\['\d{2,3}\.\d+,\d{2,3}\.\d+'\]|\['\d{2,3}\.\d+, \d{2,3}\.\d+'\]/";

    /**
     * Function for getting coordinates from the map
     * @return array
     */
    public function getMapCoordinates()
    {
        $html = file_get_contents($this->url);
        $crawler = new Crawler($html);
        $crawler->filter('html body script')->reduce(function(Crawler $node, $index){
            if($index == 26){
                preg_match_all($this->pattern1, $node->text(), $response);
                preg_match_all($this->pattern2, implode(',', $response['0']), $clearCoordinates);
                preg_match_all("/\[\d{4}\]/", implode(',', $response['0']), $marketsId);
                $coordinates = explode(', ', implode(', ', $clearCoordinates['0']));
                $result = [];
                for($i = 0; $i <= count($coordinates); $i = $i + 2)
                {
                    if(isset($coordinates[$i + 1])){
                        $result[] = ['lon' => $coordinates[$i], 'lat' => $coordinates[$i+1]];
                    }
                }
                $arr = [];
                foreach($result as $key => $test)
                {
                    $arr[] = ['lon' => $test['lon'], 'lat' => $test['lat'], 'marketId' => $marketsId[0][$key]];
                }
                return $arr; // Return the coordinates with Market id
            }
        });
    }
}
