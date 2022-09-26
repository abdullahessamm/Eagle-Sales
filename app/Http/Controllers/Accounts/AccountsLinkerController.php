<?php

namespace App\Http\Controllers\Accounts;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationError;
use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountsLinkerController extends Controller
{
    public function linkSellerToCustomer(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isSeller())
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'serial_code' => 'required|string'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $buyer = User::where('serial_code', $request->get('serial_code'))->first();
        if (! $buyer)
            throw new ValidationError(['Bad serial number']);

        if (! $buyer->isCustomer() && ! $buyer->isOnlineClient())
            throw new ValidationError(['Bad serial number']);

        if ($buyer->hasLinkedSeller())
            throw new ValidationError(['User has already linked seller']);

        $buyer->linked_seller = $authUser->id;
        $buyer->max_commissions_num_for_seller = AppConfig::maxCommissionsNumForSellers();
        $buyer->save();

        return response()->json([
            'success' => true,
            'message' => 'User linked successfully'
        ]);
    }
}
