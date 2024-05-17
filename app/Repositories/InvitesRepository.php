<?php

namespace App\Repositories;

use App\Models\Game;
use App\Models\Invite;
use App\Models\Player;

class InvitesRepository extends Repository
{
    protected $filterable = [
        'game_id',
        'player_id',
        'status',
    ];
    protected $related = [
        'game',
        'player',
    ];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(Invite::class);
    }

    public function setGame(Game $game)
    {
        $this->query->where('game_id', $game->id);
        return $this;
    }

    public function setPlayer(Player $player)
    {
        $this->query->where('player_id', $player->id);
        return $this;
    }
}
