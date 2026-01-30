<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'US Dollar',
                'symbol' => 'USD',
                'exchange_rate' => 1.0000, // Base currency
            ],
            [
                'name' => 'Mexican Peso',
                'symbol' => 'MXN',
                'exchange_rate' => 17.5000,
            ],
            [
                'name' => 'Euro',
                'symbol' => 'EUR',
                'exchange_rate' => 0.9200,
            ],
            [
                'name' => 'Canadian Dollar',
                'symbol' => 'CAD',
                'exchange_rate' => 1.3500,
            ],
            [
                'name' => 'British Pound',
                'symbol' => 'GBP',
                'exchange_rate' => 0.7900,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['name' => $currency['name']],
                $currency
            );
        }
    }
}
