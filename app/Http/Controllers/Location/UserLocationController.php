<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserLocationController extends Controller
{
    
    /**
     * get user location by ip address
     *
     * @param Request $request
     * @return void
     */
    public function getIpLocation(Request $request)
    {
        $ip = $request->ip();
        if (env('APP_DEVELOPER_MATCHINE'))
            $ip = $ip === '127.0.0.1' ? '41.40.244.201' : $ip;
        
        $location = geoip()->getLocation($ip)->toArray();
        return response()->json([
            'success' => true,
            'data' => $location
        ]);
    }
}
