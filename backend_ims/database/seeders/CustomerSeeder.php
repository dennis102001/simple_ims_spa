<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Marco Santos',
                'phone' => '0917-111-2222',
                'email' => 'marco.santos@example.com',
                'address' => '123 Innovation Street, Makati City',
            ],
            [
                'name' => 'Liza Reyes',
                'phone' => '0922-333-4444',
                'email' => 'liza.reyes@example.com',
                'address' => '45 Tech Avenue, Quezon City',
            ],
            [
                'name' => 'Carlos Dela Cruz',
                'phone' => '0933-555-6666',
                'email' => 'carlos.delacruz@example.com',
                'address' => '78 Circuit Road, Pasig City',
            ],
            [
                'name' => 'Anna Lim',
                'phone' => '0944-777-8888',
                'email' => 'anna.lim@example.com',
                'address' => '90 Gadget Street, Taguig City',
            ],
            [
                'name' => 'Joshua Navarro',
                'phone' => '0955-999-0000',
                'email' => 'joshua.navarro@example.com',
                'address' => '12 Techno Park, Mandaluyong City',
            ],
            [
                'name' => 'Maria Gonzales',
                'phone' => '0966-111-2222',
                'email' => 'maria.gonzales@example.com',
                'address' => '34 Silicon Avenue, Pasay City',
            ],
            [
                'name' => 'Daniel Flores',
                'phone' => '0977-333-4444',
                'email' => 'daniel.flores@example.com',
                'address' => '56 Electron Blvd, Manila',
            ],
            [
                'name' => 'Elaine Cruz',
                'phone' => '0988-555-6666',
                'email' => 'elaine.cruz@example.com',
                'address' => '78 Tech Park, Quezon City',
            ],
            [
                'name' => 'Kevin Villanueva',
                'phone' => '0999-777-8888',
                'email' => 'kevin.villanueva@example.com',
                'address' => '101 Innovation Drive, Makati City',
            ],
            [
                'name' => 'Sophia Ramos',
                'phone' => '0910-999-0000',
                'email' => 'sophia.ramos@example.com',
                'address' => '22 Silicon Road, Pasig City',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
