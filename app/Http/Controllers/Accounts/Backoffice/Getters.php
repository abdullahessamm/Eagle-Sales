<?php

namespace App\Http\Controllers\Accounts\Backoffice;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\BackOfficeUser;

class Getters extends Controller
{
    public function getAll()
    {
        $user = auth()->user()->userData;
        if (! $user->can('view-any', BackOfficeUser::class))
            throw new \App\Exceptions\ForbiddenException;

        $users = User::where('job', User::ADMIN_JOB_NUMBER)->get()->all();
        $backofficeUsers = [];
        
        foreach ($users as $user)
            $backofficeUsers[] = $user->withFullInfo()->showHiddens()->withPhones();

        return response()->json(['success' => true, 'users' => $backofficeUsers]);
    }

    /**
     * Get backoffice user by id
     *
     * @param integer $id
     * @return void
     */
    public function getById(int $id)
    {
        $backofficeUser = BackOfficeUser::where('user_id', $id)->first();
            if (! $backofficeUser)
                return response()->json(['success' => false], 404);
        
        $authUser = auth()->user()->userData;
        if (! $authUser->can('view', $backofficeUser))
            throw new \App\Exceptions\ForbiddenException;

        return response()->json(['success' => true, 'user' => $backofficeUser->withFullInfo()->showHiddens()->load(['phones', 'places'])]);
    }
}
