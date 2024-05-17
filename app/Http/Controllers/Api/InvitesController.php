<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invites\StoreInviteRequest;
use App\Http\Requests\Invites\UpdateInviteRequest;
use App\Models\Game;
use App\Models\Invite;
use App\Repositories\InvitesRepository;

class InvitesController extends Controller
{
    protected $invitesRepository;

    public function __construct(InvitesRepository $invitesRepository)
    {
        $this->invitesRepository = $invitesRepository;
    }

    public function index(Game $game)
    {
        $invites = $this->invitesRepository->setGame($game)->all();
        return response()->json($invites);
    }

    public function store(Game $game, StoreInviteRequest $request)
    {
        $input = $request->validated();

        $invite = Invite::where([
            'game_id' => $game->id,
            'player_id' => $input['player_id']
        ])->first();
        if($invite){
            return response()->json(['message' => __("Convite já existe")], 400);
        }

        $invite = $this->invitesRepository->create(array_merge($input, ['game_id' => $game->id]));
        return response()->json($invite, 201);
    }

    public function show(Game $game, Invite $invite)
    {
        $invite = $this->invitesRepository->find($invite->id);
        return response()->json($invite);
    }

    public function update(Game $game, Invite $invite, UpdateInviteRequest $request,)
    {
        $input = $request->all();
        $invite = $this->invitesRepository->update($invite->id, $input);
        return response()->json($invite);
    }

    public function destroy(Game $game, Invite $invite)
    {
        $this->invitesRepository->delete($invite->id);
        return response()->json(null, 204);
    }
}
