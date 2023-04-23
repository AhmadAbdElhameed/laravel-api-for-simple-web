<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:' . User::class],
            'password' => ['required','confirmed',Rules\Password::defaults()]
        ],[],[
            'name'=>"Name",
            'email'=>"Email",
            'password'=>"Password"
        ]);

        if($validator->fails()){
            return ApiResponse::sendResponse(422,"Register Validation Errors",
                $validator->messages()->all());
        }

        $user = User::create([
            "name" => $request->name,
            "email"=> $request->email,
            'password'=>Hash::make($request->password)
        ]);

        $data['token'] = $user->createToken('user_register_token')->plainTextToken;
        $data['name'] = $user->name;
        $data['email'] = $user->email;

        return ApiResponse::sendResponse(201,"User has been Registered",$data);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['required','email','string'],
            'password' => ['required']
        ],[],[
            'email'=>"Email",
            'password'=>"Password"
        ]);

        if($validator->fails()){
            return ApiResponse::sendResponse(422,"Login Validation Errors",
                $validator->errors());
        }

        if(Auth::attempt(["email"=>$request->email,'password'=>$request->password])){
            $user = Auth::user();
            $data['token'] = $user->createToken('user_login_token')->plainTextToken;
            $data['name'] = $user->name;
            $data['email'] = $user->email;
          return ApiResponse::sendResponse(200,"User LoggedIn Successfully",$data);
        }else{
            return ApiResponse::sendResponse(401,"User credentials doesn\'t exist",[]);
        }

    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::sendResponse(200,"Logged out successfully",[]);
    }
}
