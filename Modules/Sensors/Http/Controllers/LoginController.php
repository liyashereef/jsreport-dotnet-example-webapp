<?php

namespace Modules\Sensors\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Admin\Repositories\UserRepository;
use Modules\ClientApp\Http\Resources\V1\User\UserResource;


class LoginController extends Controller {

    protected $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {
        try {
            //login the user
            $user = $this->userRepository->loginForApp($request, 'motion_sensor_login');
            if (!$user) {
                return response()->json([
                    'message' => 'Invalid username or password or check whether the user has permissions'
                ], 401);
            }
            //Return access token
            return response()->json([
                'tokenType' => 'Bearer',
                'accessToken' => $user->createToken('MotionSensor')->accessToken
            ], 200);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function me()
    {
        $user = auth()->user();
        if ($user) {
            return response()->json(new UserResource($user), 200);
        }
        return response()->json([
            'message' => 'Invalid User'
        ], 401);
    }

    public function forgotPassword(Request $request)
    {
        $content = $this->userRepository->resetPassword($request);
        return response()->json($content, $content['code']);
    }

}
