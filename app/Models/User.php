<?php

namespace App\Models;

use App\Events\Accounts\UserHasBeenBanned;
use App\Events\Accounts\UserHasBeenReactivated;
use App\Events\UserHasBeenApproved;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const SUPPLIER_JOB_NUMBER = 0;
    const HIERD_SELLER_JOB_NUMBER = 1;
    const FREELANCER_SELLER_JOB_NUMBER = 2;
    const CUSTOMER_JOB_NUMBER = 3;
    const ADMIN_JOB_NUMBER = 4;
    const ONLINE_CLIENT_JOB_NUMBER = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'f_name',
        'f_name_ar',
        'l_name',
        'l_name_ar',
        'avatar_uri',
        'personal_id_uri',
        'email',
        'country',
        'city',
        'username',
        'password',
        'is_active',
        'job',
        'serial_code',
        'remember_token',
        'created_by',
        'updated_by',
        'is_approved',
        'approved_by',
        'approved_at',
        'email_verified_at',
        'last_seen',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_by',
        'updated_by',
        'approved_by',
        'personal_id_uri',
    ];

    public function getFullName()
    {
        return strtolower(ucfirst($this->f_name)) . ' ' . strtolower(ucfirst($this->l_name));
    }

    public function showHiddens()
    {
        $this->makeVisible(['created_by', 'updated_by', 'approved_by', 'personal_id_uri']);
        return $this;
    }

    public function generateSerialCode(): bool
    {
        $userLetter = '';
        switch ($this->job) {
            case 0:
                $userLetter = 'S';
                break;
            
            case 1:
                $userLetter = 'FS';
                break;
            
            case 2:
                $userLetter = 'HS';
                break;
            
            case 3:
                $userLetter = 'C';
                break;
            
            case 4:
                $userLetter = 'A';
                break;

            case 5:
                $userLetter = 'OC';
                break;
        }
        
        $serialFirstPart = $userLetter . (string) $this->id . '_';
        $this->serial_code = $serialFirstPart . \Str::random(25 - strlen($serialFirstPart));

        try {
            $this->save();
            return true;
        } catch (QueryException $e) {
            $this->delete();
            return false;
        }
    }

    public function accessTokens()
    {
        return $this->hasMany(PersonalAccessToken::class, 'user_id', 'id');
    }

    public function getAccessTokens()
    {
        return $this->accessTokens()->get()->all();
    }

    public function deleteAccessTokens()
    {
        try {
            $this->accessTokens()->delete();
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function withFullInfo()
    {
        $model = null;

        switch ($this->job) {
            case '0':
                $model = Supplier::class;
                break;
            
            case '1':
                $model = Seller::class;
                break;

            case '2':
                $model = Seller::class;
                break;
            
            case '3':
                $model = Customer::class;
                break;
            
            case '4':
                $model = BackOfficeUser::class;
                break;

            case '5':
                $model = OnlineClient::class;
                break;
        }

        $this->withPaymentCards();
        $this->userInfo = $model ? $this->hasOne($model, 'user_id', 'id')->first() : null;
        if ($this->userInfo instanceof BackOfficeUser)
            $this->userInfo->withPermissions();
        return $this;
    }

    // get related payment cards
    public function paymentCards()
    {
        return $this->hasMany(PaymentCard::class, 'user_id', 'id');
    }

    // add new payment card
    public function addPaymentCard(PaymentCard $card)
    {
        $card->user_id = $this->id;
        try {
            $card->save();
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    // with related payment cards
    public function withPaymentCards()
    {
        $this->paymentCards = $this->paymentCards()->get()->all();
        return $this;
    }

    // get related phones
    public function phones()
    {
        return $this->hasMany(Phone::class, 'user_id', 'id');
    }

    //get primary phone
    public function getPrimaryPhone()
    {
        return $this->phones()->where('is_primary', true)->first();
    }

    public function getPhones()
    {
        return $this->phones()->get()->all();
    }

    public function withPhones()
    {
        $this->phones = $this->getPhones();
        return $this;
    }

    public function sendSms(string $message, string $phone=null)
    {
        // search for specific phone if provided else get primary phone
        if ($phone) {
            $phone = $this->phones()->where('phone', $phone)->first();
            if (!$phone)
                throw new \App\Exceptions\NotFoundException(Phone::class, $phone);
        } else $phone = $this->getPrimaryPhone();

        // send sms to phone
        $phone->sendSms($message);
    }

    public function sendMail(Mailable $mail)
    {
        try {
            Mail::to($this->email)->send($mail);
        } catch (TransportException $e) {
            throw new \App\Exceptions\InternalError($e);
        }
    }

    public function banUser()
    {
        $this->is_active = false;
        $tokens = $this->getAccessTokens();
        foreach ($tokens as $token)
            cache()->forget($token->token);

        $this->deleteAccessTokens();

        try {
            $this->save();
            event(new UserHasBeenBanned($this));
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function reactivateUser()
    {
        $this->is_active = true;
        try {
            $this->save();
            event(new UserHasBeenReactivated($this));
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function approve(bool $approved=true)
    {
        $this->is_approved = $approved;
        $this->approved_by = auth()->user()->userData->id;
        $this->approved_at = now();
        
        try {
            $this->save();
            event(new UserHasBeenApproved($this));
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }
}
