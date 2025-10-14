<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CompaniesExport implements FromArray, WithHeadings, WithMapping
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return the data array for export.
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Define column headings.
     */
    public function headings(): array
    {
        return [
            'Компания',
            'Телефон',
            'Мобильный',
            'Email',
            'Сайт',
            'ИНН',
            'ОГРН',
            'Директор',
            'Рубрика',
            'Подрубрика',
            'Город',
        ];
    }

    /**
     * Map data rows for export.
     */
    public function map($row): array
    {
        return [
            $row['company'] ?? '',
            $row['phone'] ?? '',
            $row['mobile_phone'] ?? '',
            $row['Email'] ?? '',
            $row['site'] ?? '',
            $row['inn'] ?? '',
            $row['ogrn'] ?? '',
            $row['director'] ?? '',
            $row['rubric'] ?? '',
            $row['subrubric'] ?? '',
            $row['city'] ?? '',
        ];
    }
}
