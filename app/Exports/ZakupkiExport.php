<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ZakupkiExport implements FromArray, WithHeadings, WithMapping, WithColumnWidths, WithStyles
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
            'Дата запроса',
            'Товар/услуга',
            'Цена контракта',
            'Покупатель',
            'Email',
            'Телефон',
            'Адрес',
        ];
    }

    public function map($row): array
    {
        return [
            $row['date_request'] ? \Carbon\Carbon::parse($row['date_request'])->format('d.m.Y') : '-',
            $row['purchase_object'] ?? '-',
            $row['start_cost_var'] ?? ($row['start_cost'] ? number_format($row['start_cost'], 2, '.', ',') : '-'),
            $row['customer'] ?? '-',
            $row['email'] ?? '-',
            $row['phone'] ?? '-',
            $row['address'] ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Дата запроса
            'B' => 50,  // Товар/услуга
            'C' => 20,  // Цена контракта
            'D' => 40,  // Покупатель
            'E' => 30,  // Email
            'F' => 20,  // Телефон
            'G' => 40,  // Адрес
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],  // Header row bold
        ];
    }
}
