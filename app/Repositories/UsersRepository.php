<?php

namespace App\Repositories;

use App\Models\User;

class UsersRepository extends Repository
{
    protected $searchable = [
        'name',
        'email'
    ];

    protected $related = [
        'player',
    ];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(User::class);
    }
}
