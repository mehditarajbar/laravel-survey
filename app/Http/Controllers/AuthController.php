<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Login;
use App\Http\Requests\Auth\Register;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as ResponseStatusCode;


class AuthController extends Controller
{
    public function register(Register $request)
    {
        /** @var  \App\Models\User $user */
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        $token=$user->createToken('main')->plainTextToken;
        return response([
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function login(Login $request)
    {
        $remember=$request->remember??false;
        $credential=$request->except('remember');
        if (!Auth::attempt($credential,$remember)){
            return response([
                'error'=>'The Provided Credential Are Not Correct!'

            ], ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY);
        }
        $user=Auth::user();
        $token=$user->createToken('main')->plainTextToken;

        return response([
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return response([
            'success'=>true
        ]);
    }
}
