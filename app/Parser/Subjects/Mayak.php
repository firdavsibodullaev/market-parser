<?php

namespace App\Parser\Subjects;

use App\Constants\TypeShopConstant;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class Mayak extends Subject
{
    private $regions = [
        'Республика Башкортостан',
        'Республика Бурятия',
        'Республика Коми',
        'Удмуртская Республика',
        'Чеченская Республика',
        'Чувашская Республика',
        'Алтайский край',
        'Забайкальский край',
        'Краснодарский край',
        'Красноярский край',
        'Пермский край',
        'Приморский край',
        'Ставропольский край',
        'Хабаровский край',
        'Амурская область',
        'Архангельская область',
        'Белгородская область',
        'Брянская область',
        'Владимирская область',
        'Волгоградская область',
        'Вологодская область',
        'Иркутская область',
        'Калининградская область',
        'Калужская область',
        'Кемеровская область',
        'Курганская область',
        'Липецкая область',
        'Московская область',
        'Мурманская область',
        'Нижегородская область',
        'Новгородская область',
        'Новосибирская область',
        'Омская область',
        'Оренбургская область',
        'Орловская область',
        'Псковская область',
        'Ростовская область',
        'Рязанская область',
        'Самарская область',
        'Свердловская область',
        'Смоленская область',
        'Тамбовская область',
        'Тверская область',
        'Томская область',
        'Тульская область',
        'Тюменская область',
        'Челябинская область',
        'Ярославская область',
    ];
    private $cities = [
        'Москва',
        'Санкт-Петербург',
        'г. Салават',
        'г. Улан-Удэ',
        'г. Сыктывкар',
        'г. Ижевск',
        'г. Грозный',
        'г. Чебоксары',
        'г. Барнаул',
        'г. Бийск',
        'г. Рубцовск',
        'г. Чита',
        'г. Краснодар',
        'г. Сочи',
        'г. Красноярск',
        'г. Ачинск',
        'г. Ужур',
        'г. Пермь',
        'г. Владивосток',
        'г. Находка',
        'г. Уссурийск',
        'г. Ставрополь',
        'г. Хабаровск',
        'г. Комсомольск-на-Амуре',
        'г. Благовещенск',
        'г. Архангельск',
        'г. Белгород',
        'г. Старый Оскол',
        'г. Губкин',
        'г. Брянск',
        'г. Владимир',
        'г. Волгоград',
        'г. Волжский',
        'г. Вологда',
        'г. Иркутск',
        'г. Ангарск',
        'г. Братск',
        'г. Калининград',
        'г. Калуга',
        'г. Кемерово',
        'г. Ленинск-Кузнецкий',
        'г. Новокузнецк',
        'г. Киселевск',
        'г. Прокопьевск',
        'г. Курган',
        'г. Липецк',
        'г. Ногинск',
        'г. Серпухов',
        'г. Электросталь',
        'г. Подольск',
        'г. Мурманск',
        'г. Нижний Новгород',
        'г. Великий Новгород',
        'г. Новосибирск',
        'г. Толмачёво',
        'г. Омск',
        'г. Орск',
        'г. Орёл',
        'г. Псков',
        'г. Великие Луки',
        'г. Новочеркасск',
        'г. Таганрог',
        'г. Рязань',
        'г. Самара',
        'г. Екатеринбург',
        'г. Нижний Тагил',
        'г. Смоленск',
        'г. Тамбов',
        'г. Тверь',
        'г. Томск',
        'г. Тула',
        'г. Тюмень',
        'г. Челябинск',
        'г. Златоуст',
        'г. Ярославль',
        'г. Ярославль',

    ];

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url . '/contacts.html';
        $this->brand = 'Маяк';
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $html = file_get_contents($this->url);
        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);
        $filter = $crawler
            ->filter('.box_in>.text>p')
            ->reduce(function (\Symfony\Component\DomCrawler\Crawler $node, $i) {
                return !empty($node->text()) && $i >= 15 && !preg_match('/Директор|e-mail|e-mal|тел/', $node->text());
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
        $city = '';
        $region = null;
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $node = $filter->eq($i)->text();
            if (in_array($node, $this->regions)) {
                $region = $node;
                continue;
            }
            if (in_array($node, $this->cities) || preg_match('/г./', $node)) {
                $city = $node;
                continue;
            }

            $data[] = [
                'namebrand' => $this->brand,
                'post' => null,
                'uin' => Str::uuid(),
                'region' => $region,
                'city' => $this->clearSpaces($city),
                'street' => $this->clearSpaces($node),
                'typeshop' => TypeShopConstant::HYPERMARKET,
                'tc' => preg_match('/ТЦ.|Торговый центр|Торговый Центр|торговый центр/', $node) ? 'Да' : 'Нет',
            ];
        }
        return $data;
    }
}
