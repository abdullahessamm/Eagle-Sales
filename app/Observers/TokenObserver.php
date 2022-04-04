<?php

namespace App\Observers;

use App\Models\PersonalAccessToken;

class TokenObserver
{
    
    private function cacheUserInfo(PersonalAccessToken $personalAccessToken)
    {
        $clonedToken = clone $personalAccessToken;
        $clonedToken->userData = $clonedToken->getUser()->withFullInfo();
        cache()->put($clonedToken->token, $clonedToken, now()->addDays(2));
    }

    /**
     * Handle the PersonalAccessToken "created" event.
     *
     * @param  \App\Models\PersonalAccessToken  $personalAccessToken
     * @return void
     */
    public function saved(PersonalAccessToken $personalAccessToken)
    {
        $this->cacheUserInfo($personalAccessToken);
    }

    /**
     * Handle the PersonalAccessToken "retrieved" event.
     *
     * @param  \App\Models\PersonalAccessToken  $personalAccessToken
     * @return void
     */
    public function retrieved(PersonalAccessToken $personalAccessToken)
    {
        $this->cacheUserInfo($personalAccessToken);
    }

    /**
     * Handle the PersonalAccessToken "deleted" event.
     *
     * @param  \App\Models\PersonalAccessToken  $personalAccessToken
     * @return void
     */
    public function deleted(PersonalAccessToken $personalAccessToken)
    {
        cache()->forget($personalAccessToken->token);
    }
}
