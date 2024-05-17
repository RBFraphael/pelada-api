<?php

namespace Database\Seeders;

use App\Enums\InviteStatus;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = Game::all();
        $players = Player::all();

        foreach($games as $game){
            $players->random(rand(10, $players->count() - 1))->each(function($player) use ($game){
                $game->invites()->create([
                    'player_id' => $player->id,
                    'status' => InviteStatus::values()[rand(0, count(InviteStatus::values())-1)]
                ]);
            });
        }
    }
}
