<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'players_per_team'
    ];

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
