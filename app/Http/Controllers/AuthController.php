<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request) {
        
        $validator = Validator::make($request->all(), [ 
            'name'                    => 'required', 
            'email'                   => 'required|email|unique:users', 
            'password'                => 'required|confirmed|min:4'
        ],[ 'required'                => __('auth.required',['attribute'=>':attribute']),
            'unique'                  => __('auth.unique',['attribute'=>':attribute'])]);
        
        // return error if validation fails
        if ($validator->fails()) { 
            return response()->json(['error' => true, 'message' => $validator->errors()], 400);         
        } 
        
        $input = $request->all();

        //Laravel Hash class provides secure Bcrypt hashing
        $input['password'] = Hash::make($input['password']);

        // insert user data
        $user = User::create($input); 
        

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json(['error' => false, 'message' => $response], 201);
    }

    public function login(Request $request) {
        
        $validator = Validator::make($request->all(), [ 
            'email'                   => 'required|email|max:255', 
            'password'                => 'required|min:4|max:32'
        ],[ 'required'                => __('auth.required',['attribute'=>':attribute'])]);
        
        // return error if validation fails
        if ($validator->fails()) { 
            return response()->json(['error' => true, 'message' => $validator->errors()], 400);         
        }

        $fields = $request->all();

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            
            return response()->json(['error' => true, 'message' => __('auth.failed')], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        
        return response()->json(['error' => false, 'message' => $response], 200);
    }

    public function logout(Request $request) {
        
        auth()->user()->tokens()->delete();

        return response()->json(['error' => false, 'message' => __('auth.logout')], 200);

    }
}
