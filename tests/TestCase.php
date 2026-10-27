<?php

namespace Tests;

use App\Models\Currency;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createDefaultCurrency(): Currency
    {
        return Currency::query()->firstOrCreate(
            ['code' => 'EUR'],
            [
                'name' => 'Euro',
                'symbol' => '€',
                'country' => 'EU',
                'exchange_rate' => 1,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ]
        );
    }
}
