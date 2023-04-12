<?php

namespace App\Http\Controllers;

use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'matricule' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'structure'=>'required|string'

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'validation error','errors' => $validator->errors()], 422);
        }

        $role = role::where('name', $request->role)->firstOrFail();

       $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => hash::make($request->password),
            'structure_type' => $request->structure,
            'role_id' => Role::where('name', $request->role)->first()->id,
        ]);

        $token=$user->createToken('myapptoken')->plainTextToken;
        $response = [
          'user' => $user,
          'token'=> $token
        ];
        return response($response,201);

   }



}
