<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Parser\Subjects\Dobrotsen;
use App\Parser\Subjects\MariaRa;
use App\Parser\Subjects\Mayak;
use App\Parser\Subjects\Monetka;
use App\Parser\Subjects\Pobeda;
use App\Parser\Subjects\Verniy;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subject::query()->insert([
            [
                'class' => Monetka::class,
                'url' => 'https://www.monetka.ru'
            ],
            [
                'class' => MariaRa::class,
                'url' => 'https://www.maria-ra.ru'
            ],
            [
                'class' => Verniy::class,
                'url' => 'https://www.verno-info.ru'
            ],
            [
                'class' => Pobeda::class,
                'url' => 'https://xn--80aaadiigoj9aqmm.xn--p1ai'
            ],
            [
                'class' => Dobrotsen::class,
                'url' => 'https://dobrotsen.ru'
            ],
            [
                'class' => Mayak::class,
                'url' => 'https://mayakgm.ru'
            ],
        ]);
    }
}
