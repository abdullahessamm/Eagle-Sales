<?php

namespace App\Events\Interfaces;

interface ShouldNotifyListeners {
    
    /**
     * @return array<App\Models\User> array of User objects
     */
    public function getListeners(): array;

    /**
     * Get the body of notification
     *
     * @return string notification body
     */
    public function getNotificationBody(): string;
}