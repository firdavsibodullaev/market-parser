<?php

namespace App\Parser\Subjects;

class Subject
{

    /** @var string $brand */
    protected $brand;

    /** @var string $url */
    protected $url;

    /**
     * @param string $string
     * @return string
     */
    protected function clearSpaces(string $string): string
    {
        $string = str_replace(['  ', '  ', '  ', 'г.', ' ',], [' ', ' ', ' ', '', ' '], $string);
        return trim(trim($string), ',.');
    }

}
