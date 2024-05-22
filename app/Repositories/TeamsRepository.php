<?php

namespace App\Repositories;

use App\Enums\InviteStatus;
use App\Models\Game;
use App\Models\Team;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TeamsRepository extends Repository
{
    protected $searchable = [
        'name'
    ];

    protected $filterable = [
        'game_id'
    ];

    protected $related = [
        'game',
        'players',
    ];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(Team::class);
    }

    public function setGame(Game $game)
    {
        $this->query->where('game_id', $game->id);
        return $this;
    }

    /**
     * Função principal para gerar os times de um jogo
     * 
     * @param Game $game Jogo
     * @return array Times gerados
     * @throws HttpException Se não for possível dividir os jogadores em, pelo menos, dois times completos
     */
    public function generateTeams(Game $game)
    {
        $maxPlayers = $game->players_per_team;
        $confirmedInvites = $game->invites()->where('status', InviteStatus::CONFIRMED)->with('player')->get();
        $allPalayers = $confirmedInvites->pluck('player')->shuffle()->keyBy('id');
        
        // Valida se é possível dividir os jogadores em, pelo menos, dois times completos
        if($maxPlayers * 2 > $allPalayers->count()){
            throw new HttpException(400, __('Não é possível dividir os jogadores em, pelo menos, dois times completos. São necessários pelo menos ' . $maxPlayers * 2 . ' jogadores, e há apenas ' . $allPalayers->count() . ' jogadores confirmados.'));
        }

        // Calcula a quantidade de times
        $teamsCount = ceil($allPalayers->count() / $maxPlayers);
        $teams = [];

        // Valida a quantidade de goleiros
        $goalkeepers = $allPalayers->where('is_goalkeeper', true)->shuffle();
        if($goalkeepers->count() < $teamsCount){
            throw new HttpException(400, __('Não é possível dividir os goleiros igualmente entre os times. São necessários pelo menos ' . $teamsCount . ' goleiros, e há apenas ' . $goalkeepers->count() . ' goleiros confirmados.'));
        } else if($goalkeepers->count() > $teamsCount){
            throw new HttpException(400, __('Não é possível dividir os goleiros igualmente entre os times. Há mais goleiros confirmados ('.$goalkeepers->count().') do que o necessário ('.$teamsCount.').'));
        }

        // Valida a quantidade de jogadores de linha
        $linePlayers = $allPalayers->where('is_goalkeeper', false)->shuffle();
        if($linePlayers->count() < ($maxPlayers - 1) * 2){
            throw new HttpException(400, __('Não é possível dividir os jogadores de linha confirmados igualmente entre, pelo menos, dois times completos.'));
        }

        for ($i = 0; $i < $teamsCount; $i++) {
            // Criar o Time
            $team = $this->create([
                'game_id' => $game->id,
                'name' => __('Time') . ' ' . ($i + 1),
            ]);
            $teams[] = $team;

            // Atribuir um Goleiro
            $goalkeeper = $goalkeepers->shift();
            $team->players()->attach($goalkeeper);

            // Atribuir os Jogadores de Linha
            for($j = 1; $j < $maxPlayers; $j++){
                $player = $linePlayers->shift();
                if($player){
                    $team->players()->attach($player);
                }
            }
        }

        return $teams;
    }
}
