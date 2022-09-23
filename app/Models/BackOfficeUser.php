<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackOfficeUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_title'
    ];

    public function permissions()
    {
        return $this->hasOne(Permission::class, 'backoffice_user_id', 'id');
    }

    public function getPermissions()
    {
        return $this->permissions()->first();
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->first();
    }

    public function banUser()
    {
        return $this->getUser()->banUser();
    }

    public function reactiveUser()
    {
        return $this->getUser()->reactiveUser();
    }

    public function withPermissions()
    {
        $this->permissions = $this->getPermissions();
        return $this;
    }

    public function withFullInfo()
    {
        $user = $this->getUser()->withPhones();
        $user->userInfo = $this->withPermissions();
        return $user;
    }

    public function showHiddens()
    {
        return $this;
    }

}
