<?php

namespace App\Http\Controllers\Accounts\Customers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;

class Getters extends Controller
{
    public function getAll()
    {
        $authUser = auth()->user()->userData;
        
        if (! $authUser->can('view-any', Customer::class))
            throw new \App\Exceptions\ForbiddenException;

        $customers = User::where('job', User::CUSTOMER_JOB_NUMBER)->get()->all();

        foreach ($customers as $customer) {
            $customer->showHiddens()->withFullInfo()->withPhones();
            $customer->userInfo->showHiddens();
        }

        return response()->json(['success' => true, 'users' => $customers]);
    }

    public function getById(int $id)
    {
        $customer = Customer::where('user_id', $id)->first();
        if (! $customer)
            return response()->json(['success' => false], 404);

        $authUser = auth()->user()->userData;
        if (! $authUser->can('view', $customer))
            throw new \App\Exceptions\ForbiddenException;

        $user = $customer->withFullInfo();
        $user->userInfo->showHiddens();

        if ((int) $authUser->job === User::ADMIN_JOB_NUMBER)
            $user->showHiddens();

        return response()->json(['success' => true, 'user' => $user->load(['phones', 'places'])]);
    }
}
