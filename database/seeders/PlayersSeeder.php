<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::limit(36)->get()->shuffle();

        for($i = 0; $i < rand(12, 36); $i++){
            $user = $users->shift();
            Player::create([
                'user_id' => $user->id,
                'name' => fake()->firstName() . " " . fake()->lastName(),
                'level' => rand(1, 5),
                'is_goalkeeper' => fake()->boolean(25)
            ]);
        }
    }
}
