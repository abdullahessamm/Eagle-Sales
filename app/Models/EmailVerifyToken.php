<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class EmailVerifyToken extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'created_at'
    ];

    protected $dates = [
        'expires_at',
        'created_at'
    ];

    public static function createToken(int $user_id)
    {
        try {
            return static::create([
                'user_id'    => $user_id,
                'token'      => "$user_id|" . \Str::random(39 - strlen($user_id)),
                'expires_at' => now()->addHour(),
                'created_at' => now()
            ]);
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function regenerateToken(): void
    {
        $user_id = $this->user_id;
        $this->token = "$user_id|" . \Str::random(39 - strlen($user_id));
        $this->expires_at = now()->addHour();
        $this->save();
    }
}
