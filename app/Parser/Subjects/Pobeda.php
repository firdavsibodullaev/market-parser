<?php

namespace App\Parser\Subjects;

class Pobeda extends Subject
{

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url . '/контакты';
        $this->brand = 'Победа';
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return [];
    }
}
