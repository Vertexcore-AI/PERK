<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['name' => 'John Smith', 'type' => 'retail', 'phone' => '555-0101', 'email' => 'john@example.com'],
            ['name' => 'State Farm Insurance', 'type' => 'insurance', 'company' => 'State Farm', 'phone' => '555-0102', 'email' => 'claims@statefarm.com'],
            ['name' => 'Auto Parts Wholesale Inc', 'type' => 'wholesale', 'company' => 'Auto Parts Wholesale Inc', 'phone' => '555-0103', 'email' => 'orders@apw.com'],
            ['name' => 'Jane Doe', 'type' => 'retail', 'phone' => '555-0104', 'email' => 'jane@example.com'],
            ['name' => 'Progressive Insurance', 'type' => 'insurance', 'company' => 'Progressive', 'phone' => '555-0105', 'email' => 'auto@progressive.com'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
