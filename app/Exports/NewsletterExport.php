<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NewsletterExport implements FromArray, WithHeadings, WithMapping, WithColumnWidths, WithStyles
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
            'Дата создания',
            'Предмет закупки',
            'Начальная цена',
            'Заказчик',
            'Email',
            'Телефон',
            'Адрес',
            'Тип закупки',
        ];
    }

    public function map($row): array
    {
        return [
            $row['date_request'] ? \Carbon\Carbon::parse($row['date_request'])->format('d.m.Y H:i') : '-',
            $row['purchase_object'] ?? '-',
            $row['start_cost_var'] ?? ($row['start_cost'] ? number_format($row['start_cost'], 2, '.', ' ') . ' руб.' : '-'),
            $row['customer'] ?? '-',
            $row['email'] ?? '-',
            $row['phone'] ?? '-',
            $row['address'] ?? '-',
            $row['purchase_type'] ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,  // Дата создания
            'B' => 60,  // Предмет закупки
            'C' => 20,  // Начальная цена
            'D' => 40,  // Заказчик
            'E' => 30,  // Email
            'F' => 18,  // Телефон
            'G' => 40,  // Адрес
            'H' => 30,  // Тип закупки
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ],
        ];
    }
}
