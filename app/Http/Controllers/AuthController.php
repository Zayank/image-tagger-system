<?php

namespace App\Http\Controllers;

/**
 * API for the generating token for authenticating and authorizing users. This token is necessary for proceeding further with the APIs.
 * @package image-tagger
 * @subpackage AuthController
 * @author Zayan K
 *
 * @see AuthController::register() for signup
 * @see AuthController::login() for login
 * @see AuthController::logout() for logout
 * 
 */

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Rules\Password;




/*
For production

Email Validation spoof,dns

https://github.com/egulias/EmailValidator

composer require egulias/email-validator

*/


class AuthController extends Controller
{

    /**
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function register(Request $request) {
        
        
        $validator = Validator::make($request->all(), [ 
            'name'                    => 'required|string|max:255', 
            'email'                   => 'required|string|email:filter|unique:users',//email:spoof,dns 
            'password'                => ['required','min:4','max:32',new Password]
        ],[ 'required'                => __('auth.required',['attribute'=>':attribute']),
            'unique'                  => __('auth.unique',['attribute'=>':attribute'])]);
        

        // return error if validation fails
        if ($validator->fails()) { 
        
            return response()->json(['error' => true, 'message' => $validator->errors()], 400);         
        
        } 
        
        $input = $validator->valid();

        //Laravel Hash class provides secure Bcrypt hashing
        $input['password'] = Hash::make($input['password']);

        // insert user data
        $user = User::create($input); 
        

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'token' => $token
        ];

        return response()->json(['error' => false, 'data' => $response], 201);
    }
    /**
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    
    public function login(Request $request) {

        //Basic Auth used so that the credentials doesnt get logged 
        $post_data = explode(':', base64_decode(substr($request->header('authorization'), 6)));

        if (array_key_exists(0,$post_data)) {
                
                $post_data['email'] =  $post_data[0];
                
                if (array_key_exists(1,$post_data)) {
                    
                    $post_data['password'] =  $post_data[1];
                }
          }

        $validator = Validator::make($post_data, [ 
            'email'                   => 'required|string|email|max:255', 
            'password'                => 'required|string'
        ],[ 'required'                => __('auth.required',['attribute'=>':attribute'])]);
        
        // return error if validation fails
        if ($validator->fails()) { 
            
            return response()->json(['error' => true, 'message' => $validator->errors()], 401);         
        
        }

        $fields = $validator->valid();

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            
            return response()->json(['error' => true, 'message' => __('auth.failed')], 401);
        
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
        
            'token' => $token
        
        ];

        
        return response()->json(['error' => false, 'data' => $response], 200);
    }

    /**
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    
    public function logout(Request $request) {
        
        auth()->user()->tokens()->delete();

        return response()->json(['error' => false, 'message' => __('auth.logout')], 200);

    }
}
