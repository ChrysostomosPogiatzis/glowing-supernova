<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Outlet;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create a Test User
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Create Sample Company
        $company = Company::updateOrCreate(['name' => 'Nexus Corp'], [
            'address' => 'Silicon Valley, CA',
            'phone' => '555-0100',
        ]);

        // 3. Create Sample Outlets
        $outlets = [
            Outlet::updateOrCreate(['name' => 'North Branch', 'company_id' => $company->id], ['address' => 'Palo Alto']),
            Outlet::updateOrCreate(['name' => 'South Branch', 'company_id' => $company->id], ['address' => 'San Jose']),
        ];

        // 4. Create Sample Customers
        Customer::create([
            'name' => 'John',
            'surname' => 'Doe',
            'mobile_number' => '123-456-7890',
            'email' => 'john.doe@email.com',
            'company_id' => $company->id,
            'outlet_id' => $outlets[0]->id,
        ]);

        Customer::create([
            'name' => 'Jane',
            'surname' => 'Smith',
            'mobile_number' => '987-654-3210',
            'email' => 'jane.smith@email.com',
            'company_id' => $company->id,
            'outlet_id' => $outlets[1]->id,
        ]);

        Customer::create([
            'name' => 'Independent',
            'surname' => 'User',
            'mobile_number' => '555-555-5555',
            'email' => 'indie@email.com',
        ]);
    }
}
