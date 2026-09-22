<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Currency;

class LocalizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Languages
        Language::updateOrCreate(['code' => 'en'], [
            'name' => 'English',
            'direction' => 'ltr',
            'is_active' => true,
            'is_default' => true
        ]);

        Language::updateOrCreate(['code' => 'ar'], [
            'name' => 'Arabic',
            'direction' => 'rtl',
            'is_active' => true,
            'is_default' => false
        ]);

        Language::updateOrCreate(['code' => 'hi'], [
            'name' => 'Hindi',
            'direction' => 'ltr',
            'is_active' => true,
            'is_default' => false
        ]);

        // Currencies
        Currency::updateOrCreate(['code' => 'INR'], [
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'exchange_rate' => 1.0000,
            'is_active' => true,
            'is_default' => true
        ]);

        Currency::updateOrCreate(['code' => 'USD'], [
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 0.0120, // Approx
            'is_active' => true,
            'is_default' => false
        ]);
        
        Currency::updateOrCreate(['code' => 'SAR'], [
            'name' => 'Saudi Riyal',
            'symbol' => '﷼',
            'exchange_rate' => 0.0450, // Approx
            'is_active' => true,
            'is_default' => false
        ]);
    }
}
