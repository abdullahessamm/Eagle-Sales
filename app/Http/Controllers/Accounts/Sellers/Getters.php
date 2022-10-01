<?php

namespace App\Http\Controllers\Accounts\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;

class Getters extends Controller
{
    public function getAll()
    {
        $authUser = auth()->user()->userData;
        
        if (! $authUser->can('view-any', Seller::class))
            throw new \App\Exceptions\ForbiddenException;

        $sellers = User::where('job', User::FREELANCER_SELLER_JOB_NUMBER)
                    ->orWhere('job', User::HIERD_SELLER_JOB_NUMBER)->get()->all();

        foreach ($sellers as $seller) {
            $seller->showHiddens()->withFullInfo()->withPhones();
            $seller->userInfo->showHiddens();
        }

        return response()->json(['success' => true, 'users' => $sellers]);
    }

    public function getById(int $id)
    {
        $seller = Seller::where('user_id', $id)->first();
        if (! $seller)
            return response()->json(['success' => false], 404);
        $authUser = auth()->user()->userData;

        if (! $authUser->can('view', $seller))
            throw new \App\Exceptions\ForbiddenException;

        $user = $seller->showHiddens()->withFullInfo();
        if ($authUser->job === User::ADMIN_JOB_NUMBER)
            $user->showHiddens();

        return response()->json(['success' => true, 'user' => $user]);
    }
}
