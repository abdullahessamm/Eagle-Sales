<?php

namespace App\Http\Controllers\Accounts\Customers;

use App\Http\Controllers\Accounts\UserUpdater;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\ArabicLettersWithSpaces;

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

        $customer = Customer::where('id', $request->get('id'))->first();

        if (! $customer)
            return response()->json(['success' => false], 404);

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update-full-data', $customer))
            throw new \App\Exceptions\ForbiddenException;

        $user = $customer->getUser();

        return UserUpdater::updateInfo($request, $user);
    }

    public function updateUserInfo(Request $request)
    {
        $rules = [
            'id'                    => 'required|integer',
            'shop_name'             => 'string|min:3|max:50',
            'l1_address'            => 'regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l1_address_ar'         => [ new ArabicLettersWithSpaces, 'min:4', 'max:255'],
            'l2_address'            => 'regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l2_address_ar'         => [new ArabicLettersWithSpaces, 'min:4', 'max:255'],
            'location_coords'       => 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/',
            'vat_no'                => 'regex:/^[A-Za-z]{2}\d{3,18}$/|unique:suppliers,vat_no',
            'category_id'           => 'integer|between:1,255',
            'shop_space'            => 'numeric|between:1,9999.99',
            'credit_limit'          => 'numeric|between:1,9999999999.99',
        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $customer = Customer::where('id', $request->get('id'))->first();
        
        if (! $customer)
            return response()->json(['success' => false], 404);

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update-full-data'))
            throw new \App\Exceptions\ForbiddenException;

        unset($rules['id']);
        foreach ($rules as $param => $rule)
            $customer->$param = request()->get($param) ?? $customer->$param;

        try {
            $customer->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' => true]);
    }
}
