<?php

namespace App\Models\Traits;

use App\Models\User;

trait UsersTrait {

    public function showHiddens()
    {
        $this->makeVisible($this->hidden);
        return $this;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getUser()
    {
        return $this->user()->first();
    }

    public function withFullInfo()
    {
        $user = $this->getUser()->withPhones();
        $user->userInfo = $this;
        if ($user->job === User::SUPPLIER_JOB_NUMBER)
            $user->userInfo = $this->withRelatedOrders();
        return $user;
    }

    public function getRelatedOrders()
    {
        return $this->relatedOrders()->get()->all();
    }

    public function withRelatedOrders()
    {
        $user = clone $this;
        $user->orders = $user->getRelatedOrders();
        return $user;
    }

    public function getCurrencyAttribute()
    {
        return $this->getUser()->currency;
    }
}