<?php

namespace Database\Seeders;

use App\Models\Interest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interests = [
            'Football',
            'Basketball',
            'Music',
            'Reading',
            'Gaming',
            'Travel',
            'Coding',
            'Photography',
            'Cooking',
            'Swimming',
        ];

        foreach ($interests as $interest) {
            Interest::firstOrCreate([
                'name' => $interest,
            ]);
        }
    }
}
