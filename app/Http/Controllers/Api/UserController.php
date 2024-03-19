<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (Auth::attempt($credentials)) {
                $user = auth()->user();
                $token = $user->createToken('MA_CLE_SECRETE_VISIBLE_A_MOI_AU_BACK-END')
                ->plainTextToken;

                return response()->json([
                    'status_code' => 200,
                    'message' => 'L\'Utilisateur '. $user->name .' est connecté.',
                    'token' => $token
                ]);
            }
            else {
                return response()->json([
                    'status_code' => 403,
                    'message' => 'L\'email ou le mot de passe incorrect.'
                ]);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    } 

    public function register(RegisterRequest $request)
    {
        try {
            $valdData = $request->validated();
            $valdData['password'] = Hash::make($request->password, [
                'rounds' => 12
            ]);
            User::create($valdData);

            return response()->json([
                'status_code' => 200,
                'message' => 'L\'utilisateur a été ajouté.',
                'user' => User::get()->last()
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
    
    public function logout()
    {
        Auth::logout();

        return response()->json([
            'status_code' => 200,
            'message' => 'L\'utilisateur est déconnecté.'
        ]);
    }
}
