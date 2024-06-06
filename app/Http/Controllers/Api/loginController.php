<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class loginController extends Controller
{
    //
    public function register(Request $request) {
       
        $validation = Validator::make($request->all(), [
            "username" => "required",
            "password" => "required"
        ]);

        if ($validation->fails()) {
            $data = [
                "message" => "Data not valid",
                "error" => $validation->errors(),
                "status" => 400
            ];

            return response()->json($data, 400);
        }
       
        $user = new User();

        $user->username = $request->username;
        $user->password = Hash::make($request->password);

        $user->save();
        $data = [
            "message"=> "User created",
            "data" => $user,
            "status"=> 200
        ];

        Auth::login($user);
        return response()->json( $data, 200);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'username',
            'password'
        ]);
        // IF VALIDATION FAILS
        if ($validator->fails()) {
            $data = [
                'message' => 'Data not valid',
                'error' => $validator->errors(),
                'status'=> 422,
            ];
            return response()->json($data, 422);
        }

        $auth = Auth::attempt($request->only('username', 'password'));
        if (!$auth) {
            $data = [
                'message' => 'Invalid credentials!',
                'status' => 401
            ];

            return response()->json($data, 401);
        }

        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;

        $data = [
            "message" => "Login successful",
            "access_token" => $token,
            "status" => 200
         ];
        
        return response()->json($data, 200);
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $data = [
            "message" => "Logout successful",
            "status" => 200
        ];



        return response()->json($data,200);
    }
}
