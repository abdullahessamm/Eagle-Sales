<?php

namespace App\Observers;

use App\Mail\Account\VerifyMail;
use App\Models\EmailVerifyToken;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        try {
            $mailToken = EmailVerifyToken::createToken($user->id);
            Mail::to($user->email)->send(new VerifyMail($mailToken->token, $user->id));
        } catch (TransportException $e) {
            $user->delete();
            throw new \App\Exceptions\MailException($e);
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $tokens = PersonalAccessToken::where('user_id', $user->id)->get()->all();
        if (count($tokens) === 0)
            return;

        foreach ($tokens as $token) {
            if (! cache()->has($token->token))
                break;

            $token->userData = $user->withFullInfo();
            cache()->put($token->token, $token);
        }
    }
}
