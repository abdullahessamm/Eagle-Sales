<?php

namespace App\Mail\Account;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    private string $token;
    private int $userID;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $token, int $userID)
    {
        $this->token = $token;
        $this->userID = $userID;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.account.VerifyMail')
                    ->with('token', $this->token)
                    ->with('userID', $this->userID);
    }
}
