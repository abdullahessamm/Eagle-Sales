<?php

namespace App\Http\Controllers;

use App\Models\Phone;
use App\Models\Errors\InternalError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhoneController extends Controller
{

    public function confirm(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+[0-9]{11,14}$/',
            'verify_code' => 'required|regex:/^[0-9]{6}$/'
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $record = Phone::where('phone', $request->get('phone'))->first();
        
        if (! $record)
            return response()->json(['success' => false], 404);

        if ((string) $record->verify_code !== (string) $request->get('verify_code'))
            return response()->json(['success' => false], 400);

        if (! $record->verify_code_expires_at->isFuture())
            return response()->json(['success' => false], 410);

        $record->verify_code = null;
        $record->verify_code_expires_at = null;
        $record->verified_at = now();
        $record->save();

        return response()->json(['success' => true]);
    }

    public function resendCode(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+[0-9]{11,14}$/exists:phones,phone'
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $phone = Phone::where('phone', $request->get('phone'))->first();

        if ($phone->verified_at)
            return response()->json(['success' => false], 400);

        if ((int) $phone->user_id !== (int) auth()->user()->userData->id)
            throw new \App\Exceptions\ForbiddenException;

        $phone->sendVerifyCode();

        return response()->json(['success'=>true]);
    }
}
