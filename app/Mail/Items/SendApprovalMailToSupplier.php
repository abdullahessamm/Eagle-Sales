<?php

namespace App\Mail\Items;

use App\Models\Item;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendApprovalMailToSupplier extends Mailable
{
    use Queueable, SerializesModels;

    public Item $item;
    public User $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Item $item, User $user)
    {
        $this->item = $item;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.items.approvalMail')
        ->subject('Item Approval Response')
        ->with([
            'item' => $this->item,
            'user' => $this->user,
        ]);
    }
}
