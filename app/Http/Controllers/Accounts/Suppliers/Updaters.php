<?php

namespace App\Http\Controllers\Accounts\Suppliers;

use App\Http\Controllers\Accounts\UserUpdater;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class Updaters extends Controller
{
    public function updateAccountInfo(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|integer'
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $supplier = Supplier::where('user_id', $request->get('id'))->first();
        if (! $supplier)
            return response()->json(['success' => false, 'msg' =>'suppllier not found'], 404);

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $supplier))
            throw new \App\Exceptions\ForbiddenException;
        
        $user = $supplier->getUser();
        return UserUpdater::updateInfo($request, $user);
    }

    public function updateUserInfo(Request $request)
    {
        $rules = [
            'id'               => 'required|integer',
            'shop_name'        => 'string|min:3|max:50',
            'whatsapp_no'      => 'regex:/^\+966[0-9]{8,11}$/|unique:suppliers,whatsapp_no',
            'fb_page'          => 'regex:/^(https?:\/\/)?(www\.)?(m\.)?(fb)?(facebook)?(\.com)(\/[\w\D]+\/?)+$/|unique:suppliers,fb_page',
            'website_domain'   => 'regex:/^(https?:\/\/)?(([\da-z])+\.)?[\d\w\-]+\.[a-z]{2,3}$/|unique:suppliers,website_domain',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $supplier = Supplier::where('user_id', $request->get('id'))->first();
        if (! $supplier)
            return response()->json(['success' => false, 'msg' => 'supplier not found'], 404);

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $supplier))
            throw new \App\Exceptions\ForbiddenException;

        unset($rules['id']);
        foreach ($rules as $param => $rule)
            $supplier->$param = request()->get($param) ?? $supplier->$param;
        
        try {
            $supplier->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' => true]);
    }
}
