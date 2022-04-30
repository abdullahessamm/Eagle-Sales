<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Str;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    /**
     * @var User
     */
    private User $user;

    public function login(Request $request)
    {
        $rules = [
            'username' => 'regex:/^[A-Za-z0-9]{4,50}$/|required_without:email',
            'email'    => 'email|required_without:username',
            'password' => 'required|string|min:8|max:80'
        ];
        $reqValidate = Validator::make($request->all(), $rules);

        if ($reqValidate->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $reqValidate->errors()->all()
            ], 400);
        }

        $credentials = ['password' => $request->get('password')];
        
        $request->has('username') ?
            $credentials['username'] = $request->get('username'):
            $credentials['email']    = $request->get('email');

        if (auth()->guard('web')->attempt($credentials))
        {
            $this->user = auth()->guard('web')->user();
            
            //handle not active users requests
            if (! $this->checkIfAccountIsActive())
                return $this->responseToNotActiveUser();

            $generatedToken = $this->generateToken($request);

            // response with serial access token 
            return response()->json([
                "success" => true,
                "token"   => $generatedToken->serial_access_token
            ]);
        }

        return response()->json(['success' => false], 401);
    }

    /**
     * generate access tokens function
     *
     * @param Request $request
     * @return PersonalAccessToken
     */
    private function generateToken(Request $request): PersonalAccessToken
    {
        //token record data
        $token = [
            'user_id'             => $this->user->id,
            'token'               => $this->user->id . '|' . Str::random(49 - strlen($this->user->id)),
            'serial_access_token' => $this->user->id . '|' . Str::random(49 - strlen($this->user->id)),
            'serial'              => $this->user->id . '|' . Str::random(39 - strlen($this->user->id)),
            'user_agent'          => filter_var($request->server('HTTP_USER_AGENT'), FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'device_ip'           => $request->ip(),
            'expires_at'          => now()->addYears(10),
            'last_use'            => now(),
            'serial_access_token_expires_at' => now()->addMinutes(1),
        ];

        $generatedToken = PersonalAccessToken::create($token); //create the token!
        return $generatedToken;
    }

    /**
     * Check if Account is active or not
     *
     * @return boolean
     */
    private function checkIfAccountIsActive(): bool
    {
        return $this->user->is_active && $this->user->is_approved;
    }

    /**
     * 
     * Error code will be `0`, `1` describes as following
     * `0` if account denied by admins.
     * `1` if account banned
     */
    private function responseToNotActiveUser()
    {
        $errorCode = null;
        
        if ($this->user->is_approved === 0) //account denied
            $errorCode = 0;
        
        if (! $this->user->is_active) //account banned
            $errorCode = 1;

        return response()->json([
            'success'   => false,
            'errorCode' => $errorCode
        ], 403);
    }

    /**
     * @param string $serialAccessToken
     * @return Response
     */
    public function getSerial(string $serialAccessToken)
    {
        $tokenRecord = PersonalAccessToken::where('serial_access_token', $serialAccessToken)->first();
        
        if (! $tokenRecord)
            return response()->json(['success' => false], 404);
        
        if (! $tokenRecord->serial_access_token_expires_at->isFuture())
        {
            return response()->json(['success' => false], 410);
        }

        $tokenRecord->serial_access_token_expires_at = now();

        try {
            $tokenRecord->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
        
        return response()->json([
            'success' => true,
            'token'   => $tokenRecord->token,
            'serial'  => $tokenRecord->serial,
            'user'    => $tokenRecord->getFullUserData(),
        ]);
    }

    public function getAuthedUserInfo()
    {
        return response()->json(['success'=> true, "user" => auth()->user()->userData]);
    }

    public function logout()
    {
        $tokenRecord = PersonalAccessToken::where('token', auth()->user()['token'])->where('serial', auth()->user()['serial']);
        $tokenRecord->delete();
        cache()->forget(auth()->user()['token']);
        return response()->json(['success' => true]);
    }

}
