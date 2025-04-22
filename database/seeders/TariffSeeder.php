<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Наполнение тарифов VPN
 */
class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('tariffs')->insert([
            [
                'count_month' => 1,
                'status' => true,
            ],
            [
                'count_month' => 3,
                'status' => true,
            ],
            [
                'count_month' => 6,
                'status' => true,
            ],
        ]);
    }
}
