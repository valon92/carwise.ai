<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $currencies = [
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'country' => 'United States', 'exchange_rate' => 1.000000, 'is_active' => true, 'is_default' => true, 'sort_order' => 1],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'country' => 'European Union', 'exchange_rate' => 0.850000, 'is_active' => true, 'is_default' => false, 'sort_order' => 2],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'country' => 'United Kingdom', 'exchange_rate' => 0.730000, 'is_active' => true, 'is_default' => false, 'sort_order' => 3],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'country' => 'Japan', 'exchange_rate' => 110.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 4],
            ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'country' => 'China', 'exchange_rate' => 6.450000, 'is_active' => true, 'is_default' => false, 'sort_order' => 5],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'country' => 'Canada', 'exchange_rate' => 1.250000, 'is_active' => true, 'is_default' => false, 'sort_order' => 6],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'country' => 'Australia', 'exchange_rate' => 1.350000, 'is_active' => true, 'is_default' => false, 'sort_order' => 7],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'country' => 'Switzerland', 'exchange_rate' => 0.920000, 'is_active' => true, 'is_default' => false, 'sort_order' => 8],
            ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr', 'country' => 'Sweden', 'exchange_rate' => 8.500000, 'is_active' => true, 'is_default' => false, 'sort_order' => 9],
            ['code' => 'NOK', 'name' => 'Norwegian Krone', 'symbol' => 'kr', 'country' => 'Norway', 'exchange_rate' => 8.800000, 'is_active' => true, 'is_default' => false, 'sort_order' => 10],
            ['code' => 'DKK', 'name' => 'Danish Krone', 'symbol' => 'kr', 'country' => 'Denmark', 'exchange_rate' => 6.300000, 'is_active' => true, 'is_default' => false, 'sort_order' => 11],
            ['code' => 'PLN', 'name' => 'Polish Zloty', 'symbol' => 'zł', 'country' => 'Poland', 'exchange_rate' => 3.950000, 'is_active' => true, 'is_default' => false, 'sort_order' => 12],
            ['code' => 'CZK', 'name' => 'Czech Koruna', 'symbol' => 'Kč', 'country' => 'Czech Republic', 'exchange_rate' => 21.500000, 'is_active' => true, 'is_default' => false, 'sort_order' => 13],
            ['code' => 'HUF', 'name' => 'Hungarian Forint', 'symbol' => 'Ft', 'country' => 'Hungary', 'exchange_rate' => 300.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 14],
            ['code' => 'RON', 'name' => 'Romanian Leu', 'symbol' => 'lei', 'country' => 'Romania', 'exchange_rate' => 4.200000, 'is_active' => true, 'is_default' => false, 'sort_order' => 15],
            ['code' => 'BGN', 'name' => 'Bulgarian Lev', 'symbol' => 'лв', 'country' => 'Bulgaria', 'exchange_rate' => 1.660000, 'is_active' => true, 'is_default' => false, 'sort_order' => 16],
            ['code' => 'HRK', 'name' => 'Croatian Kuna', 'symbol' => 'kn', 'country' => 'Croatia', 'exchange_rate' => 6.400000, 'is_active' => true, 'is_default' => false, 'sort_order' => 17],
            ['code' => 'RSD', 'name' => 'Serbian Dinar', 'symbol' => 'дин', 'country' => 'Serbia', 'exchange_rate' => 100.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 18],
            ['code' => 'ALL', 'name' => 'Albanian Lek', 'symbol' => 'L', 'country' => 'Albania', 'exchange_rate' => 105.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 19],
            ['code' => 'MKD', 'name' => 'Macedonian Denar', 'symbol' => 'ден', 'country' => 'North Macedonia', 'exchange_rate' => 52.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 20],
            ['code' => 'BAM', 'name' => 'Bosnia and Herzegovina Mark', 'symbol' => 'KM', 'country' => 'Bosnia and Herzegovina', 'exchange_rate' => 1.660000, 'is_active' => true, 'is_default' => false, 'sort_order' => 21],
            ['code' => 'KRW', 'name' => 'South Korean Won', 'symbol' => '₩', 'country' => 'South Korea', 'exchange_rate' => 1180.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 22],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'country' => 'India', 'exchange_rate' => 74.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 23],
            ['code' => 'THB', 'name' => 'Thai Baht', 'symbol' => '฿', 'country' => 'Thailand', 'exchange_rate' => 33.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 24],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'country' => 'Singapore', 'exchange_rate' => 1.350000, 'is_active' => true, 'is_default' => false, 'sort_order' => 25],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'country' => 'Malaysia', 'exchange_rate' => 4.200000, 'is_active' => true, 'is_default' => false, 'sort_order' => 26],
            ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'country' => 'Indonesia', 'exchange_rate' => 14200.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 27],
            ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => '₱', 'country' => 'Philippines', 'exchange_rate' => 50.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 28],
            ['code' => 'VND', 'name' => 'Vietnamese Dong', 'symbol' => '₫', 'country' => 'Vietnam', 'exchange_rate' => 23000.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 29],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'country' => 'Brazil', 'exchange_rate' => 5.200000, 'is_active' => true, 'is_default' => false, 'sort_order' => 30],
            ['code' => 'ARS', 'name' => 'Argentine Peso', 'symbol' => '$', 'country' => 'Argentina', 'exchange_rate' => 98.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 31],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$', 'country' => 'Mexico', 'exchange_rate' => 20.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 32],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'country' => 'South Africa', 'exchange_rate' => 14.500000, 'is_active' => true, 'is_default' => false, 'sort_order' => 33],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'country' => 'United Arab Emirates', 'exchange_rate' => 3.670000, 'is_active' => true, 'is_default' => false, 'sort_order' => 34],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => '﷼', 'country' => 'Saudi Arabia', 'exchange_rate' => 3.750000, 'is_active' => true, 'is_default' => false, 'sort_order' => 35],
            ['code' => 'QAR', 'name' => 'Qatari Riyal', 'symbol' => '﷼', 'country' => 'Qatar', 'exchange_rate' => 3.640000, 'is_active' => true, 'is_default' => false, 'sort_order' => 36],
            ['code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'symbol' => 'د.ك', 'country' => 'Kuwait', 'exchange_rate' => 0.300000, 'is_active' => true, 'is_default' => false, 'sort_order' => 37],
            ['code' => 'BHD', 'name' => 'Bahraini Dinar', 'symbol' => 'د.ب', 'country' => 'Bahrain', 'exchange_rate' => 0.380000, 'is_active' => true, 'is_default' => false, 'sort_order' => 38],
            ['code' => 'OMR', 'name' => 'Omani Rial', 'symbol' => '﷼', 'country' => 'Oman', 'exchange_rate' => 0.385000, 'is_active' => true, 'is_default' => false, 'sort_order' => 39],
            ['code' => 'JOD', 'name' => 'Jordanian Dinar', 'symbol' => 'د.ا', 'country' => 'Jordan', 'exchange_rate' => 0.710000, 'is_active' => true, 'is_default' => false, 'sort_order' => 40],
            ['code' => 'LBP', 'name' => 'Lebanese Pound', 'symbol' => 'ل.ل', 'country' => 'Lebanon', 'exchange_rate' => 1500.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 41],
            ['code' => 'ILS', 'name' => 'Israeli Shekel', 'symbol' => '₪', 'country' => 'Israel', 'exchange_rate' => 3.250000, 'is_active' => true, 'is_default' => false, 'sort_order' => 42],
            ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺', 'country' => 'Turkey', 'exchange_rate' => 8.500000, 'is_active' => true, 'is_default' => false, 'sort_order' => 43],
            ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽', 'country' => 'Russia', 'exchange_rate' => 73.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 44],
            ['code' => 'UAH', 'name' => 'Ukrainian Hryvnia', 'symbol' => '₴', 'country' => 'Ukraine', 'exchange_rate' => 27.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 45],
            ['code' => 'BYN', 'name' => 'Belarusian Ruble', 'symbol' => 'Br', 'country' => 'Belarus', 'exchange_rate' => 2.550000, 'is_active' => true, 'is_default' => false, 'sort_order' => 46],
            ['code' => 'KZT', 'name' => 'Kazakhstani Tenge', 'symbol' => '₸', 'country' => 'Kazakhstan', 'exchange_rate' => 425.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 47],
            ['code' => 'UZS', 'name' => 'Uzbekistani Som', 'symbol' => 'лв', 'country' => 'Uzbekistan', 'exchange_rate' => 10500.000000, 'is_active' => true, 'is_default' => false, 'sort_order' => 48]
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }

        $this->command->info('Currencies seeded successfully!');
    }
}