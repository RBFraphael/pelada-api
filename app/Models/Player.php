<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'level',
        'is_goalkeeper'
    ];

    protected function casts() {
        return [
            'is_goalkeeper' => 'boolean'
        ];
    }

    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_players');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
