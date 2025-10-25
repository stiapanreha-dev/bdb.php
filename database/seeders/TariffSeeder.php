<?php

namespace Database\Seeders;

use App\Models\Tariff;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tariffs = [
            [
                'name' => 'Недельный',
                'duration_days' => 7,
                'price' => 300.00,
                'is_active' => true,
            ],
            [
                'name' => 'Месячный',
                'duration_days' => 30,
                'price' => 1000.00,
                'is_active' => true,
            ],
        ];

        foreach ($tariffs as $tariff) {
            Tariff::updateOrCreate(
                ['duration_days' => $tariff['duration_days']],
                $tariff
            );
        }

        $this->command->info('Базовые тарифы созданы: Недельный (300 руб) и Месячный (1000 руб)');
    }
}
