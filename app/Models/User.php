<?php

namespace App\Models;

use App\Events\Accounts\UserHasBeenBanned;
use App\Events\Accounts\UserHasBeenReactivated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

class User extends Authenticatable
{
    use HasFactory;

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
        'l_name',
        'avatar_uri',
        'avatar_pos',
        'email',
        'username',
        'password',
        'is_active',
        'job',
        'serial_code',
        'created_by',
        'updated_by',
        'is_approved',
        'approved_by',
        'approved_at',
        'lang',
        'gender',
        'email_verified_at',
        'last_seen',
        'linked_seller',
        'max_commissions_num_for_seller',
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

    /**
     * set the user's avatar
     *
     * @param string $uri
     * @param integer $x
     * @param integer $y
     * @param integer $scale
     * @return void
     */
    public function setAvatar(string $uri, int $x, int $y, int $scale)
    {
        $this->avatar_uri = $uri;
        $this->avatar_pos = [
            'x' => $x,
            'y' => $y,
            'scale' => $scale
        ];
    }

    /**
     * delete the user's avatar
     *
     * @return void
     */
    public function deleteAvatar()
    {
        $this->avatar_uri = null;
        $this->avatar_pos = null;
    }

    public function isSupplier()
    {
        return $this->job === self::SUPPLIER_JOB_NUMBER;
    }

    public function isHierdSeller()
    {
        return $this->job === self::HIERD_SELLER_JOB_NUMBER;
    }

    public function isFreelancerSeller()
    {
        return $this->job === self::FREELANCER_SELLER_JOB_NUMBER;
    }

    public function isSeller()
    {
        return $this->isHierdSeller() || $this->isFreelancerSeller();
    }

    public function isCustomer()
    {
        return $this->job === self::CUSTOMER_JOB_NUMBER;
    }

    public function isAdmin()
    {
        return $this->job === self::ADMIN_JOB_NUMBER;
    }

    public function isOnlineClient()
    {
        return $this->job === self::ONLINE_CLIENT_JOB_NUMBER;
    }

    public function generateUniqueUsername()
    {
        $this->username = 'user_' . now()->timestamp;

        while ($this->where('username', $this->username)->exists()) {
            $this->username = 'user_' . now()->timestamp;
        }
    }

    public function generateSerialCode(): bool
    {
        $userLetter = '';
        switch ($this->job) {
            case self::SUPPLIER_JOB_NUMBER:
                $userLetter = 'S';
                break;
            
            case self::HIERD_SELLER_JOB_NUMBER:
                $userLetter = 'HS';
                break;
            
            case self::FREELANCER_SELLER_JOB_NUMBER:
                $userLetter = 'FS';
                break;
            
            case self::CUSTOMER_JOB_NUMBER:
                $userLetter = 'C';
                break;
            
            case self::ADMIN_JOB_NUMBER:
                $userLetter = 'A';
                break;

            case self::ONLINE_CLIENT_JOB_NUMBER:
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
        }

        $this->userInfo = $model ? $this->hasOne($model, 'user_id', 'id')->first() : null;
        if ($this->userInfo instanceof BackOfficeUser)
            $this->userInfo->withPermissions();
        return $this;
    }

    // related places
    public function places()
    {
        return $this->hasMany(UsersPlace::class, 'user_id', 'id');
    }

    public function getPlaces()
    {
        return $this->places()->get()->all();
    }

    public function deletePlace($placeId)
    {
        try {
            $this->places()->where('place_id', $placeId)->delete();
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function addPlace(UsersPlace $place)
    {
        try {
            $place->user_id = $this->id;
            $place->save();
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function getPrimaryPlace()
    {
        return $this->places()->where('is_primary', true)->first();
    }

    public function getCurrencyAttribute()
    {
        $countryCode = $this->getPrimaryPlace()->country_code;
        $country = AvailableCountry::where('iso_code', $countryCode)->first();
        return $country->currency;
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
            return true;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    // Notification section

    public function notifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id', 'id');
    }

    public function getNotifications(array $columns = ['*'])
    {
        return $this->notifications()->get($columns);
    }

    public function getNotification(int $id)
    {
        return $this->notifications()->find($id);
    }

    public function notify(string $eventName, string $body)
    {
        $this->notifications()->create([
            'event' => $eventName,
            'body' => $body
        ]);
    }

    // invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'buyer_id', 'id');
    }

    public function createInvoice(Invoice $invoice)
    {
        return $this->invoices()->save($invoice);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function linkedSeller()
    {
        return $this->belongsTo(User::class, 'linked_seller', 'id');
    }

    public function hasLinkedSeller()
    {
        return (bool) $this->linked_seller;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function linkedCustomers()
    {
        return $this->hasMany(User::class, 'linked_seller', 'id');
    }

    public function orders()
    {
        $foreignCol = $this->isSeller() ? 'created_by' : 'buyer_id';
        return $this->hasMany(Order::class, $foreignCol, 'id');
    }

    /**
     * @return boolean
     */
    public function sellerReachedCommissionLimit()
    {
        if (! $this->max_commissions_num_for_seller)
            return true;
        
        return (integer) $this->orders()->where('state', Order::STATUS_DELIVERED)->count()
            >= (integer) $this->max_commissions_num_for_seller;
    }
}
