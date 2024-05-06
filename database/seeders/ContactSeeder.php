<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Phone;
use App\Models\Email;
use App\Models\Address;
use Faker\Factory as Faker;

class ContactSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        for ($i = 0; $i < 5000; $i++) {
            // Crear contacto
            $contact = Contact::create([
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'city' => $faker->city
            ]);

            // Crear número de telefono
            for ($j = 0; $j < 10; $j++) {
                Phone::create([
                    'phone' => $faker->phoneNumber,
                    'contact_id' => $contact->id,
                ]);
            }

            // Crear email
            Email::create([
                'email' => $faker->email,
                'contact_id' => $contact->id,
            ]);

            // Crear dirección
            Address::create([
                'address' => $faker->address,
                'contact_id' => $contact->id,
            ]);
        }
    }
}
