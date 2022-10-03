<?php

namespace App\Http\Controllers\Accounts\Sellers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Seller;
use App\Http\Controllers\Accounts\UserUpdater;
use App\Models\User;
use App\Rules\ArabicLettersWithSpaces;
use Illuminate\Database\QueryException;

class Updaters extends Controller
{
    public function updateAccountInfo(Request $request)
    {

        $rules = [
            'id' => 'required|integer'
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $seller = Seller::where('user_id', $request->get('id'))->first();

        if (! $seller)
            return response()->json(['success' => false], 404);

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $seller))
            throw new \App\Exceptions\ForbiddenException;

        $user = $seller->getUser();

        return UserUpdater::updateInfo($request, $user);
    }

    public function updateUserInfo(Request $request)
    {
        $authUser = auth()->user()->userData;

        $rules = [
            'id'                  => 'required|integer',
            'age'                 => 'integer|between:16,100',
            'education'           => 'string|min:4|max:255',
            'bank_account_number' => 'required_with:bank_name|regex:/^[A-Za-z0-9]{9,50}$/',
            'bank_name'           => 'required_with:bank_account_number|string|min:3|max:50'
        ];

        $validation = Validator::make($request->all(), $rules);
        
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        if (count($request->all()) <= 2)
            return response()->json(['success' => false, 'msg' => 'No changes to be updated'], 400);

        $seller = Seller::where('user_id', $request->get('id'))->first();

        if (! $seller)
            return response()->json(['success' => false], 404);

        if (! $authUser->can('update', $seller))
            throw new \App\Exceptions\ForbiddenException;

        unset($rules['id']);
        foreach ($rules as $param => $rule)
            $seller->$param = request()->get($param) ?? $seller->$param;

        try {
            $seller->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' =>true]);
    }

    public function updateHiredSeller(Request $request)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->job !== User::ADMIN_JOB_NUMBER)
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'id' => 'required|integer',
            'salary' => 'numeric|between:0,99999.99',
            'tax' => 'numeric|between:0,9999.99',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $seller = Seller::where('id', $request->get('id'))->first();
        if (! $seller)
            return response()->json(['success' => false], 404);

        if ($authUser->can('update', $seller))
            throw new \App\Exceptions\ForbiddenException;

        unset($rules['id']);
        foreach ($rules as $param => $rule)
            $seller->$param = request()->get($param) ?? $seller->$param;

        try {
            $seller->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' => true]);
    }
}
