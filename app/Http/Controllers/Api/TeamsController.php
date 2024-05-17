<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\UpdateTeamRequest;
use App\Models\Game;
use App\Models\Team;
use App\Repositories\TeamsRepository;
use Illuminate\Http\Request;

class TeamsController extends Controller
{
    protected $teamsRepository;

    public function __construct(TeamsRepository $teamsRepository)
    {
        $this->teamsRepository = $teamsRepository;
    }

    public function index(Game $game)
    {
        $teams = $this->teamsRepository->setGame($game)->all();
        return response()->json($teams);
    }

    public function store(Game $game)
    {
        $this->teamsRepository->setGame($game)->deleteAll();
        $teams = $this->teamsRepository->generateTeams($game);
        return response()->json($teams, 201);
    }

    public function show(Game $game, Team $team)
    {
        $team = $this->teamsRepository->setGame($game)->find($team->id);
        return response()->json($team);
    }

    public function update(Game $game, Team $team, UpdateTeamRequest $request)
    {
        $input = $request->validated();
        $team = $this->teamsRepository->setGame($game)->update($team->id, $input);
        return response()->json($team);
    }

    public function destroy(Game $game, Team $team)
    {
        $this->teamsRepository->delete($team->id);
        return response()->json(null, 204);
    }
}
