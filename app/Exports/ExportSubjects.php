<?php

namespace App\Exports;

use App\Models\Address;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportSubjects implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Address::query()->get();
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->namebrand,
            $row->region,
            $row->city,
            $row->street,
            $row->post,
            $row->tc,
            $row->uin,
            $row->typeshop
        ];
    }

    public function headings(): array
    {
        return [
            'Название',
            'Название брена',
            'Регион',
            'Город',
            'Улица',
            'Почтовый индекс',
            'Торговый центр',
            'uin',
            'Тип магазина'
        ];
    }
}
