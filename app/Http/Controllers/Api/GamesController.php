<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Games\StoreGameRequest;
use App\Http\Requests\Games\UpdateGameRequest;
use App\Models\Game;
use App\Repositories\GamesRepository;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    protected $gamesRepository;

    public function __construct(GamesRepository $gamesRepository)
    {
        $this->gamesRepository = $gamesRepository;
    }

    public function index()
    {
        $games = $this->gamesRepository->all();
        return response()->json($games);
    }

    public function store(StoreGameRequest $request)
    {
        $input = $request->validated();
        $game = $this->gamesRepository->create($input);
        return response()->json($game, 201);
    }

    public function show(Game $game)
    {
        $game = $this->gamesRepository->find($game->id);
        return response()->json($game);
    }

    public function update(UpdateGameRequest $request, Game $game)
    {
        $input = $request->validated();
        $game = $this->gamesRepository->update($game->id, $input);
        return response()->json($game);
    }

    public function destroy(Game $game)
    {
        $this->gamesRepository->delete($game->id);
        return response()->json(null, 204);
    }
}
