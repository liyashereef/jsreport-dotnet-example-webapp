<?php

namespace  Modules\VisitorLog\Http\Controllers\API;

use Illuminate\Http\Request;
use Modules\Admin\Repositories\UserRepository;
use Modules\VisitorLog\Transformers\UserAuthResource;

class AuthController
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {
        try {
            //login the user
            $user = $this->userRepository->loginForApp($request, 'visitorlog_app_admin');
            if (!$user) {
                return response()->json([
                    'message' => 'Invalid Username or Password or Check whether the user has permissions'
                ], 401);
            }

            //Access token setup
            $tr = $user->createToken('MyApp');
            $token = $tr->token;
            $token->expires_at = now()->addYear(1);
            $token->save();

            //Return access token
            return response()->json([
                'tokenType' => 'Bearer',
                'accessToken' => $tr->accessToken,
                'id' => $user->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        //Revoke api tokens
        auth()->user()->token()->revoke();
        //Revoke web tokens
        auth()->guard('web')->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ])->status(200);
    }

    public function me()
    {
        $user = auth()->user();
        if ($user) {
            return response()->json(new UserAuthResource($user), 200);
        }
        return response()->json([
            'message' => 'Invalid User'
        ], 401);
    }
}
