<?php

namespace App\Repositories;

use App\Models\Player;

class PlayersRepository extends Repository
{
    protected $searchable = [
        'name'
    ];

    protected $filterable = [
        'level',
        'is_goalkeeper',
    ];

    protected $related = [
        'invites',
        'teams',
    ];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(Player::class);
    }
}
