<?php

namespace App\Parser\Subjects;

use Illuminate\Support\Str;

class Monetka extends Subject
{

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url . '/contacts';
        $this->brand = 'Монетка';
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $html = file_get_contents($this->url);

        $crawler = new \Symfony\Component\DomCrawler\Crawler($html);

        $nodes = $crawler->filter('address > p')->first()->html();

        $elements = explode('<br>', $nodes);

        $elements = $this->getElements($elements);

        return $this->getParsedData($elements);
    }

    /**
     * @param array $elements
     * @return array
     */
    protected function getElements(array $elements): array
    {
        foreach ($elements as $key => $element) {
            $element = trim(preg_replace('/\s+/', ' ', $element));
            if (!$element) {
                unset($elements[$key]);
                continue;
            }
            $elements[$key] = $element;
        }
        $elements = array_values($elements);
        return array_chunk($elements, 2);
    }

    /**
     * @param array $elements
     * @return array
     */
    protected function getParsedData(array $elements): array
    {
        $data = [];

        foreach ($elements as $element) {
            list($post, $address) = explode(',', $element[1], 2);
            $address_parts = explode(',', trim($address));
            if (count($address_parts) == 3) {
                $data[] = [
                    'namebrand' => $this->brand,
                    'name' => $element[0],
                    'post' => $post,
                    'uin' => Str::uuid(),
                    'region' => null,
                    'city' => $this->clearSpaces($address_parts[0]),
                    'street' => $address = "{$this->clearSpaces($address_parts[1])}, {$this->clearSpaces($address_parts[2])}",
                    'tc' => preg_match('/ТЦ.|Торговый центр|Торговый Центр|торговый центр/', $address) ? 'Да' : 'Нет',
                ];
            } elseif (count($address_parts) == 4) {
                $data[] = [
                    'namebrand' => $this->brand,
                    'name' => $element[0],
                    'post' => $post,
                    'uin' => Str::uuid(),
                    'region' => $this->clearSpaces($address_parts[0]),
                    'city' => $this->clearSpaces($address_parts[1]),
                    'street' => $address = "{$this->clearSpaces($address_parts[2])}, {$this->clearSpaces($address_parts[3])}",
                    'tc' => preg_match('/ТЦ.|Торговый центр|Торговый Центр|торговый центр/', $address) ? 'Да' : 'Нет',
                ];
            }
        }

        return $data;
    }
}
