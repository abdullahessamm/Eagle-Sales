<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'serial_access_token',
        'serial',
        'user_agent',
        'device_ip',
        'device_mac',
        'expires_at',
        'last_use',
        'serial_access_token_expires_at',
    ];

    protected $dates = [
        'expires_at',
        'last_use',
        'serial_access_token_expires_at',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUser()
    {
        return $this->User()->first();
    }

    public function withFullUserData()
    {
        $this->userData = $this->getUser()->withFullInfo();
        $this->userData->userInfo->showHiddens();
        return $this;
    }

    public function getFullUserData()
    {
        return $this->getUser()->withFullInfo();
    }

    public function getAuthIdentifier()
    {
        return $this->getUser();
    }
}
