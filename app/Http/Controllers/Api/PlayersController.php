<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Players\StorePlayerRequest;
use App\Http\Requests\Players\UpdatePlayerRequest;
use App\Models\Player;
use App\Repositories\PlayersRepository;
use Illuminate\Http\Request;

class PlayersController extends Controller
{
    protected $playersRepository;

    public function __construct(PlayersRepository $playersRepository)
    {
        $this->playersRepository = $playersRepository;
    }

    public function index()
    {
        $players = $this->playersRepository->all();
        return response()->json($players);
    }

    public function store(StorePlayerRequest $request)
    {
        $input = $request->validated();
        $player = $this->playersRepository->create($input);
        return response()->json($player, 201);
    }

    public function show(Player $player)
    {
        $player = $this->playersRepository->find($player->id);
        return response()->json($player);
    }

    public function update(UpdatePlayerRequest $request, Player $player)
    {
        $input = $request->validated();
        $player = $this->playersRepository->update($player->id, $input);
        return response()->json($player);
    }

    public function destroy(Player $player)
    {
        $this->playersRepository->delete($player->id);
        return response()->json(null, 204);
    }
}
