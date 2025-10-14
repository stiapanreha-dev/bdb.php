<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CompanyExport implements FromArray, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Компания',
            'Рубрика',
            'Подрубрика',
            'Город',
            'Телефон',
            'Мобильный',
            'Email',
            'Сайт',
            'ИНН',
            'ОГРН',
            'Директор',
        ];
    }

    public function map($row): array
    {
        return [
            $row['company'] ?? '-',
            $row['rubric'] ?? '-',
            $row['subrubric'] ?? '-',
            $row['city'] ?? '-',
            $row['phone'] ?? '-',
            $row['mobile_phone'] ?? '-',
            $row['Email'] ?? '-',
            $row['site'] ?? '-',
            $row['inn'] ?? '-',
            $row['ogrn'] ?? '-',
            $row['director'] ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,  // Компания
            'B' => 30,  // Рубрика
            'C' => 30,  // Подрубрика
            'D' => 25,  // Город
            'E' => 20,  // Телефон
            'F' => 20,  // Мобильный
            'G' => 30,  // Email
            'H' => 30,  // Сайт
            'I' => 15,  // ИНН
            'J' => 18,  // ОГРН
            'K' => 30,  // Директор
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],  // Header row bold
        ];
    }
}
