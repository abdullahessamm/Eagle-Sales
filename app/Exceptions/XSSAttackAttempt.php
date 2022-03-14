<?php

namespace App\Exceptions;

use App\Models\AttackAttempt;
use Exception;

class XSSAttackAttempt extends Exception
{
    public function report()
    {
        $token = auth()->user();
        if ($token)
            $token->delete();

        $attemptRecord = new AttackAttempt;
        $attemptRecord->attack_type = 'XSS';
        $attemptRecord->ip = request()->ip();
        $attemptRecord->user_id = $token ? $token->userData->id : null;
        $attemptRecord->attempt_uri = request()->getRequestUri();
        $attemptRecord->setAttemptTime();
        $attemptRecord->save();

        
    }

    public function render()
    {
        return response()->json(['success' => false, 'msg' => 'your ip has been blocked'], 403);
    }
}
