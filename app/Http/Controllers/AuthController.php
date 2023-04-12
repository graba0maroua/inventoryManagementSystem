<?php

namespace App\Http\Controllers;

use App\Models\DemandeCompte;
use App\Models\role;
use App\Models\User;
use App\Models\Centre;
use App\Models\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
       //*REGISTRATION //
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'matricule' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:Admin,Chef_équipe,Chef_centre,Chef_unité',
            'structure_type' => 'required|string',
            'structure_id' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'validation error','errors' => $validator->errors()], 422);
        }

        $role = Role::where('name', $request->role)->firstOrFail();

        // Retrieve the structure based on its type and ID
        if ($request->structure_type === 'Centre') {
            $structureType = 'App\Models\Centre';
        } elseif ($request->structure_type === 'Unite') {
            $structureType = 'App\Models\Unite';
        } else {
            return response()->json(['message' => 'Invalid structure type'], 422);
        }

        $structure = $structureType::find($request->structure_id);
        if ($structure === null) {
            return response()->json(['message' => 'Structure not found'], 404);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'matricule' => $request->matricule,
            'role_id' => $role->id,
            // 'structure_type' => get_class($structure),
            'structure_type' => $structureType,
            'structure_id' => $structure->getKey(),

        ]);

         // Create a new account demande for the user
         $demandeCompte = DemandeCompte::create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user,'demandeCompte'=>$demandeCompte]);
   }

   //*LOGIN //
   public function login(Request $request){
    $validator = Validator::make($request->all(),
    [   'matricule' => 'required',
        'password' => 'required'
    ]);
    if ($validator->fails()) {
        return response()->json(['message' => 'validation error','errors' => $validator->errors()], 422);
    }

    $user= User::where('matricule',$request->matricule)->first();
    if (!$user) {
        return response()->json([
            'status' => false,
            'message' => 'User not found.',
        ], 401);
    }

    if (!$user->Compte_isActivated) {
        return response()->json(['message' => 'Your account has not been activated yet'], 401);
    }

    $demandeCompte = $user->demandeCompte;
    if (!$demandeCompte || $demandeCompte->status !== 'accepted') {
        return response()->json(['message' => 'Your Account has not been accepted yet'], 401);
    }
    //! Esq je laisse les 2 fonctions seules wela je test les 2 au meme temps

    if (!Auth::attempt($request->only(['matricule', 'password']))) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token=$user->createToken('myapptoken')->plainTextToken;
    $response = [
        'message' => 'User Logged In Successfully',
        'user' => $user,
        'token'=> $token
    ];
    return response($response,201);

}
  public function logout(Request $request){
    $request->user()->currentAccessToken()->delete();
    return response()->json(['success' => true,], 200);
}
}
