<?php

namespace App\Http\Controllers;

use App\Models\DemandeCompte;
use App\Models\role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'matricule' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:Admin,Chef_équipe,Chef_centre,Chef_unité',
            'structure_type' => 'required|string|in:Centre,Unite',
            'structure_id' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'validation error','errors' => $validator->errors()], 422);
        }

        $role = Role::where('name', $request->role)->firstOrFail();

        // Retrieve the structure based on its type and ID
        $structureType = $request->structure_type;
        $structureId = $request->structure_id;
        $structure = $structureType::find($structureId);
        if ($structure === null) {
            return response()->json(['message' => 'Structure not found'], 404);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'matricule' => $request->matricule,
            'role_id' => $role->id,
            'structure_type' => get_class($structure),
            'structure_id' => $structure->getKey(),
        ]);

         // Create a new account demande for the user
         $demandeCompte = DemandeCompte::create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user,'demandeCompte'=>$demandeCompte]);
   }



}
