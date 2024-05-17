<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function index(Request $request)
    {
        $users = $this->usersRepository->all();
        return response()->json($users);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->usersRepository->create($request->validated());
        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        $user = $this->usersRepository->find($user->id);
        return response()->json($user);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        $user = $this->usersRepository->update($user->id, $request->validated());
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $this->usersRepository->delete($user->id);
        return response()->json(null, 204);
    }
}
