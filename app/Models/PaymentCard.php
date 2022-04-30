<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCard extends Model
{
    use HasFactory;
    
    protected $connection = 'sqlite';

    protected $fillable = [
        'user_id',
        'card_number',
        'cvv',
        'card_type',
        'card_holder_name',
        'expiry_date',
        'is_default',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'card_expiry_date',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'user_id',
        'card_number',
        'cvv',
        'created_at',
        'updated_at'
    ];

    // get card number as asterisk except last 4 digits
    public function getCardNumberAsHidden()
    {
        return str_repeat('*', strlen($this->card_number) - 4) . substr($this->card_number, -4);
    }

    // auto detect card type
    public function setCardType()
    {
        $card_number = $this->card_number;
        $card_type = 'unknown';

        if (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/', $card_number)) {
            $card_type = 'visa';
        } elseif (preg_match('/^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$/', $card_number)) {
            $card_type = 'mastercard';
        }

        $this->card_type = $card_type;
    }
}
