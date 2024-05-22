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
        // Obtém a quantidade máxima de jogadores por time
        $maxPlayers = $game->players_per_team;

        // Obtém dos jogadores confirmados
        $players = $game->invites()->where('status', InviteStatus::CONFIRMED)->with('player')->get()->pluck('player');

        // Valida se é possível dividir os jogadores em, pelo menos, dois times completos
        if(($maxPlayers * 2) > $players->count()){
            throw new HttpException(400, __('Não é possível dividir os jogadores em, pelo menos, dois times completos. São necessários pelo menos ' . $maxPlayers * 2 . ' jogadores, e há apenas ' . $players->count() . ' jogadores confirmados.'));
        }

        // Calcula a quantidade total (completos e parciais) de times
        $teamsCount = ceil($players->count() / $maxPlayers);

        // Verifica se é necessário criar um time parcial
        $hasPartialTeam = ($players->count() / $maxPlayers) > 0;

        // Valida a quantidade de goleiros
        $goalkeepers = $players->where('is_goalkeeper', true);
        if($goalkeepers->count() < $teamsCount){
            throw new HttpException(400, __('Não é possível dividir os goleiros igualmente entre os times. São necessários pelo menos ' . $teamsCount . ' goleiros, e há apenas ' . $goalkeepers->count() . ' goleiros confirmados.'));
        } else if($goalkeepers->count() > $teamsCount){
            throw new HttpException(400, __('Não é possível dividir os goleiros igualmente entre os times. Há mais goleiros confirmados ('.$goalkeepers->count().') do que o necessário ('.$teamsCount.').'));
        }
        $goalkeepers = array_values($goalkeepers->toArray()); // Converte para array e reindexa

        // Valida a quantidade de jogadores de linha
        $fieldPlayers = $players->where('is_goalkeeper', false)->sortByDesc('level');
        if($fieldPlayers->count() < ($maxPlayers - 1) * 2){
            throw new HttpException(400, __('Não é possível dividir os jogadores de linha confirmados igualmente entre, pelo menos, dois times completos.'));
        }
        $fieldPlayers = array_values($fieldPlayers->toArray()); // Converte para array e reindexa

        // Decrementa a quantidade de times se houver um time parcial
        if($hasPartialTeam){
            $teamsCount--;
        }

        // Inicializa os times
        $teams = array_fill(0, $teamsCount, []);
        $teamLevels = array_fill(0, $teamsCount, 0); // Array auxiliar paa balancear os times
        $finalTeams = [];

        // Divide os goleiros entre os times
        for($i = 0; $i < count($teams); $i++){
            // Remove e retorna o primeiro goleiro da lista
            $goalkeeper = array_shift($goalkeepers);
            // Adiciona o goleiro ao time
            $teams[$i][] = $goalkeeper;
            // Atualiza o nível do time
            $teamLevels[$i] += $goalkeeper['level'];
        }

        // Divide os jogadores de linha entre os times
        $fieldPlayersCount = count($fieldPlayers);
        for($i = 0; $i < $fieldPlayersCount; $i++){
            // Valida se ainda há times disponíveis
            if(count($teams) > 0){
                // Remove e retorna o primeiro jogador da lista
                $player = array_shift($fieldPlayers);

                // Encontra o time com o menor nível
                $teamIndex = array_search(min($teamLevels), $teamLevels);

                // Adiciona o jogador ao time
                $teams[$teamIndex][] = $player;

                // Atualiza o nível do time
                $teamLevels[$teamIndex] += $player['level'];

                // Verifica se o time está completo
                if(count($teams[$teamIndex]) == $maxPlayers){
                    // Adiciona o time à lista de times completos
                    $finalTeams[] = $teams[$teamIndex];
                    // Remove o time da lista de times disponíveis e do array auxiliar
                    unset($teams[$teamIndex]);
                    unset($teamLevels[$teamIndex]);
                }
            }
        }

        // Verifica se é necessário criar um time parcial
        if($hasPartialTeam){
            // Cria um time parcial
            $partialTeam = [];
            
            // Verifica se ainda há goleiros disponíveis
            if(count($goalkeepers) > 0){
                // Adiciona o goleiro restante ao time parcial
                $partialTeam[] = array_shift($goalkeepers);
            }

            // Adiciona os jogadores restantes ao time parcial
            foreach($fieldPlayers as $player){
                $partialTeam[] = $player;
            }

            // Adiciona o time parcial à lista de times
            $finalTeams[] = $partialTeam;
        }

        
        // Persiste os times no banco de dados
        $teamModels = collect([]); // Array auxiliar para armazenar os times persistidos
        foreach($finalTeams as $i => $team){
            $teamModel = $this->create([
                'game_id' => $game->id,
                'name' => __('Time') . ' ' . ($i + 1),
            ]);

            foreach($team as $player){
                $teamModel->players()->attach($player['id']);
            }

            $teamModels->push($teamModel);
        }

        // Retorna os times persistidos
        return $teamModels;
    }
}
