<?php

namespace App\Repositories;

use App\Models\Game;

class GamesRepository extends Repository
{
    protected $filterable = [
        'date',
    ];

    protected $searchable = [
        'date',
    ];

    protected $related = [
        'invites',
        'teams'
    ];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(Game::class);
    }
}
