<?php

namespace App\Http\Controllers\Accounts\Suppliers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\User;

class Getters extends Controller
{
    /**
     * get all registered suppliers
     *
     * @return void
     */
    public function getAll()
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->can('viewAny', Supplier::class))
            throw new \App\Exceptions\ForbiddenException;

        $suppliers = User::where('job', User::SUPPLIER_JOB_NUMBER)->get()->all();

        foreach ($suppliers as $supplier) {
            $supplier->showHiddens()->withFullInfo();
            $supplier->userInfo->showHiddens();
        }

        return response()->json(['success' => true, 'users' => $suppliers]);
    }

    /**
     * get supplier by id
     *
     * @param integer $id
     * @return void
     */
    public function getById(int $id)
    {
        $supplier = Supplier::where('user_id', $id)->first();
        if (! $supplier)
            return response()->json(['success' => false, 'msg' => 'user not found'], 404);

        $authUser = auth()->user()->userData;
        if (! $authUser->can('view', $supplier))
            throw new \App\Exceptions\ForbiddenException;

        $user = $supplier->withFullInfo();
        $user->userInfo->showHiddens();
        if ($authUser->job === User::ADMIN_JOB_NUMBER)
            $user->showHiddens;

        return response()->json(['success' =>true, 'user' => $user]);
    }
}
