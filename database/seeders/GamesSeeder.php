<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0; $i < rand(3, 9); $i++){
            Game::create([
                'date' => fake()->dateTimeBetween("+1 day", "+1 month")->format('Y-m-d'),
                'players_per_team' => rand(5, 11),
            ]);
        }
    }
}
