<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
    ];

    protected $appends = [
        'average_level',
        'total_level',
        'players_count',
    ];

    public function getAverageLevelAttribute()
    {
        return $this->players->avg('level');
    }

    public function getTotalLevelAttribute()
    {
        return $this->players->sum('level');
    }

    public function getPlayersCountAttribute()
    {
        return $this->players->count();
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'team_players');
    }
}
