<?php

namespace App\Auth;

use App\Models\PersonalAccessToken;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class CacheTokenProvider extends EloquentUserProvider
{
    public function __construct(Hasher $hasher)
    {
        parent::__construct($hasher, PersonalAccessToken::class);
    }

    public function retrieveByCredentials(array $credentials)
    {
        // if (cache()->has($credentials['token']))
        //     return cache()->get($credentials['token']);

        // return auth user if cache expired
        $query = $this->createModel();
        $query = $query->where('token', $credentials['token']);

        if ($user = $query->first())
            return $user->withFullUserData();
        return null;
    }
}