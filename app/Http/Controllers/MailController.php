<?php

namespace App\Http\Controllers;

use App\Models\EmailVerifyToken;
use App\Models\User;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function verifyMail($userID, Request $request)
    {
        if (! $request->has('token'))
            return response('Bad request', 400);
        
        $token = EmailVerifyToken::where('user_id', $userID)->first();
        if (! $token)
            return response('Bad request', 400);

        if ($request->get('token') !== $token->token)
            return response('Bad request', 400);

        if (! $token->expires_at->isFuture())
            return response('token expired', 410);
        
        $user = User::where('id', $userID)->firstOrFail();
        $user->email_verified_at = now();
        $user->save();
        $token->delete();

        if ($user->job !== User::CUSTOMER_JOB_NUMBER)
            event(new \App\Events\Accounts\NewUserArrived($user));

        return response('Email Vrified!');
    }
}
