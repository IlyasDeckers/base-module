<?php

namespace IlyasDeckers\BaseModule\Modules\Auth\Http\Controllers;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use IlyasDeckers\BaseModule\BaseController;
use Illuminate\Http\Request;
use Clockwork\Users\Models\User;
use Carbon\Carbon;
use Response;
use Auth;

class AuthController extends BaseController
{
    use ThrottlesLogins;

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $user = User::with(['roles.permissions', 'permissions'])->where('email', $request->email)->first();
        
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if(!Auth::attempt(request(['email', 'password']))) {
            return Response::json(['message' => 'Wrong credentials'], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $tokenResult->token->expires_at = Carbon::now()->addWeeks(1);
        $tokenResult->token->save();

        return response()->json([
            'user' => $user->toArray(),
            'venice' => config('venice.config'),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ], 200);
    }

    public function hello(Request $request)
    {
        $user = User::with(['roles.permissions', 'permissions'])
            ->findOrFail($request->user()->id);

        return response()->json([
            'user' => $user->toArray(),
            'venice' => config('venice.config'),
        ]);
    }

    public function username()
    {
        return 'email';
    }
}
