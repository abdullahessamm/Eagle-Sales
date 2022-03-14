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

    public function getPermissions()
    {
        return $this->hasOne(Permission::class, 'backoffice_user_id', 'id')->first();
    }

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->first();
    }

    public function withPermissions()
    {
        $this->permissions = $this->getPermissions();
        return $this;
    }
}
