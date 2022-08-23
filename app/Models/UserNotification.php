<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event',
        'body',
        'link',
        'icon',
        'delivered_at',
        'read_at'
    ];

    public $dates = [
        'delivered_at',
        'read_at'
    ];

    protected $casts = [
        'body' => 'array',
    ];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->first();
    }

    public function getUserAttribute()
    {
        return $this->getUser();
    }

    public function read()
    {
        $this->read_at = now();
        $this->save();
    }

    /**
     * Determine if the notification has been delivered.
     *
     * @return boolean
     */
    public function isDelivered()
    {
        return $this->delivered_at !== null;
    }
    
    public function getIsDeliveredAttribute()
    {
        return $this->isDelivered();
    }
    
    /**
     * Determine if the notification has been read.
     *
     * @return boolean
     */
    public function isRead()
    {
        return $this->read_at !== null;
    }

    public function getIsReadAttribute()
    {
        return $this->isRead();
    }


}
