<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'ip';
    public $incincrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ip',
        'reason',
        'blocked_at'
    ];

    public function blockIfNotBlocked(string $ip, string $reason)
    {
        $blockedIP = $this->where('ip', $ip)->first();

        if ($blockedIP)
            return;

        $this->ip = $ip;
        $this->reason = $reason;
        $this->blocked_at = now();
        $this->save();
        
    }
}
