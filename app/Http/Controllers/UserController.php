<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request){ 
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            // Este es otro tipo de respuesta
            // $success['token'] =  $user->createToken('prueba')->accessToken;
            $dataResponse = array(
                'token' => $user->createToken('prueba')->accessToken,
                'status' => $this->successStatus
            );
            return response()->json(['success' => $dataResponse], $this->successStatus); 
            // Este es otro tipo de respuesta
            // return response()->json(['success' => $success, 'status' => $this->successStatus], $this->successStatus); 

        }else{
            $this->successStatus = 401;
            return response()->json(['error'=>'Unauthorised', 'status' => $this->successStatus],  $this->successStatus); 
        }
    }

    public function register(Request $request){ 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('prueba')-> accessToken; 
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this-> successStatus); 
    }

    public function details() { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 
}
