<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Players\StorePlayerRequest;
use App\Http\Requests\Players\UpdatePlayerRequest;
use App\Mail\PlayerRegisteredMail;
use App\Models\Player;
use App\Repositories\PlayersRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PlayersController extends Controller
{
    protected $playersRepository;
    protected $usersRepository;

    public function __construct(PlayersRepository $playersRepository, UsersRepository $usersRepository)
    {
        $this->playersRepository = $playersRepository;
        $this->usersRepository = $usersRepository;
    }

    public function index()
    {
        $players = $this->playersRepository->all();
        return response()->json($players);
    }

    public function store(StorePlayerRequest $request)
    {
        $input = $request->validated();
        $userName = explode("@", $input['user']['email'])[0];
        $user = $this->usersRepository->create([
            'name' => $userName,
            'email' => $input['user']['email'],
            'password' => bcrypt($input['user']['password']),
        ]);

        $player = $this->playersRepository->create([
            'user_id' => $user->id,
            'name' => $input['name'],
            'level' => $input['level'],
            'is_goalkeeper' => $input['is_goalkeeper'],
        
        ]);
        if($player){
            // TODO: Adicionar Queue para envio de e-mail - Necessária hospedagem com suporte a filas
            Mail::to($player->user->email)->send(new PlayerRegisteredMail($player->user, $input['user']['password']));
        }

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
