<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ResponseModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Login $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return ResponseModel::error('Unauthorized', 401);
            }
        } catch (JWTException $e) {
            return ResponseModel::error('Could not create token', 500);
        }

        return ResponseModel::success(compact('token'));
    }

    public function profile()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return ResponseModel::success(compact('user'));
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return ResponseModel::success(\null, 'Logout Success');
    }
}
