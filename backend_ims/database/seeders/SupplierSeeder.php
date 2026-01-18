<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'TechZone Supplies',
                'contact_person' => 'Alice Ramirez',
                'phone' => '0917-123-4567',
                'email' => 'alice@techzone.com',
                'address' => '123 Tech Street, Makati City',
            ],
            [
                'name' => 'Gadget World Inc.',
                'contact_person' => 'Ben Cruz',
                'phone' => '0922-234-5678',
                'email' => 'ben@gadgetworld.com',
                'address' => '45 Gadget Avenue, Quezon City',
            ],
            [
                'name' => 'Electronix Hub',
                'contact_person' => 'Carla Tan',
                'phone' => '0933-345-6789',
                'email' => 'carla@electronixhub.com',
                'address' => '78 Circuit Road, Pasig City',
            ],
            [
                'name' => 'Digital Solutions Co.',
                'contact_person' => 'David Lim',
                'phone' => '0944-456-7890',
                'email' => 'david@digitalsolutions.com',
                'address' => '90 Techno Park, Taguig City',
            ],
            [
                'name' => 'NextGen Electronics',
                'contact_person' => 'Ella Gomez',
                'phone' => '0955-567-8901',
                'email' => 'ella@nextgenelectronics.com',
                'address' => '12 Innovation Street, Mandaluyong',
            ],
            [
                'name' => 'Alpha Tech Supplies',
                'contact_person' => 'Frank Santos',
                'phone' => '0966-678-9012',
                'email' => 'frank@alphatech.com',
                'address' => '34 Silicon Avenue, Pasay City',
            ],
            [
                'name' => 'Circuit Masters',
                'contact_person' => 'Grace Navarro',
                'phone' => '0977-789-0123',
                'email' => 'grace@circuitmasters.com',
                'address' => '56 Electron Blvd, Manila',
            ],
            [
                'name' => 'Smart Devices Co.',
                'contact_person' => 'Henry Flores',
                'phone' => '0988-890-1234',
                'email' => 'henry@smartdevices.com',
                'address' => '78 Tech Park, Quezon City',
            ],
            [
                'name' => 'Innovatek',
                'contact_person' => 'Irene Cruz',
                'phone' => '0999-901-2345',
                'email' => 'irene@innovatek.com',
                'address' => '101 Innovation Drive, Makati City',
            ],
            [
                'name' => 'ProTech Suppliers',
                'contact_person' => 'James Villanueva',
                'phone' => '0910-012-3456',
                'email' => 'james@protech.com',
                'address' => '22 Silicon Road, Pasig City',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
