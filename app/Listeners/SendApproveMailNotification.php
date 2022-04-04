<?php

namespace App\Listeners;

use App\Mail\Account\UserApprovedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

class SendApproveMailNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $user = $event->user;
            Mail::to($user->email)->send(new UserApprovedMail($user));
        } catch (TransportException $e) {
            throw new \App\Exceptions\MailException($e);
        }
    }
}
