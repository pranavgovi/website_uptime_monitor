<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Website;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = Client::create([
            'email' => 'client1@example.com'
        ]);

        $client1->websites()->createMany([
            ['url' => 'https://google.com', 'is_up' => true],
            ['url' => 'https://github.com', 'is_up' => true],
        ]);

        $client2 = Client::create([
            'email' => 'client2@example.com'
        ]);

        $client2->websites()->createMany([
            ['url' => 'https://stackoverflow.com', 'is_up' => true],
        ]);
    }
}
