<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AuthController extends Controller
{
    public function register(RegisterRequest $registerRequest){
        $data               = $registerRequest->all();
        $data['password']   = Hash::make($registerRequest->password);
        $user               = User::create($data);
        $userDetails        = UserDetails::create([
                                'user_id'   => $user->id,
                                'user_type' => $user->user_type,
                                'status'    => 0
                            ]);
        if($user){
            $user->token    = $user->createToken('laravel-launchpad')->accessToken;
            return response()->json([
                'user'      => $user,
                'status'    => 'success',
                'message'   => 'User created successfully'
            ]);
        } else {
            return response()->json([
                'user'      => new stdClass(),
                'status'    => 'failure',
                'message'   => 'Something went wrong'
            ]);
        }
    }

    public function login(LoginRequest $loginRequest){
        $user = User::where('email',$loginRequest->email)->first();
        if(empty($user)){
            return response()->json([
                'user'      => new stdClass(),
                'status'    => 'failure',
                'message'   => 'User not found'
            ]);
        }
        if(Auth::attempt(['email' => $loginRequest->email, 'password' => $loginRequest->password])){
            $user->token    = $user->createToken('laravel-launchpad')->accessToken;;
            return response()->json([
                'user'      => $user,
                'status'    => 'success',
                'message'   => 'User created successfully'
            ]);
        } else {
            return response()->json([
                'user'      => new stdClass(),
                'status'    => 'failure',
                'message'   => 'Invalid login credentials'
            ]);
        }
    }

    public function logout(Request $request) {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json([
            'status'    => 'success',
            'message'   => 'User logout successfully'
        ]);
    }
}
