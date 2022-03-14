<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'f_name',
        'l_name',
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
    ];

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
        }
        
        $serialFirstPart = $userLetter . '_' . $this->id . '|';
        $this->serial_code = $serialFirstPart . \Str::random(50 - strlen($serialFirstPart));

        try {
            $this->save();
            return true;
        } catch (QueryException $e) {
            $this->delete();
            return false;
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
}
