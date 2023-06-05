<?php

namespace App\Http\Controllers;
use App\Mail\NewCompte;
use App\Models\DemandeCompte;
use App\Models\role;
use App\Models\User;
use App\Models\Centre;
use App\Models\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
       //*REGISTRATION //
    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string|unique:users',
            'matricule' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'structure_id' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'validation error','errors' => $validator->errors()], 422);
        }

        $role = Role::where('name', $request->role)->firstOrFail();

        // Retrieve the structure based on its type and ID
        if ($request->role === 'Chef_centre') {
            $structureType = 'App\Models\Centre';
            $structure = $structureType::where('COP_LIB',$request->structure_id)
            ->value('COP_ID');
        } elseif ($request->role === 'Chef_unité') {
            $structureType = 'App\Models\Unite';
            $structure = $structureType::where('UCM_LIB',$request->structure_id)
            ->value('UCM_ID');
        }elseif($request->role === 'Chef_équipe') {
            $structureType = 'App\Models\Centre';
            $structure = $structureType::where('COP_LIB',$request->structure_id)
            ->value('COP_ID');
        }
            else {
            return response()->json(['message' => 'Invalid structure type'], 422);
        }
   if ($structure === null) {
            return response()->json(['message' => 'Structure not found'], 404);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'matricule' => $request->matricule,
            'role_id' => $role->id,

            'structure_type' => $structureType,
            'structure_id' => $structure

        ]);

         // Create a new account demande for the user
         $demandeCompte = DemandeCompte::create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);


            Mail::to('grabamaroua@gmail.com')->send(new NewCompte($user));


        return response()->json(['user' => $user]);
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
    if ($user->role_id==4) { //if user is Admin
        $token=$user->createToken('myapptoken')->plainTextToken;
        $response = [
            'message' => 'Admin Logged In Successfully',
            'token'=> $token

        ];
        return response($response,201);
    }
    if (!$user->Compte_isActivated) {
        return response()->json(['message' => 'Your account is not activated'], 401);
    }

    $demandeCompte = $user->demandeCompte;
    if (!$demandeCompte || $demandeCompte->status !== 'accepted') {
        return response()->json(['message' => 'Your Account has not been accepted yet'], 401);
    }

    if (!Auth::attempt($request->only(['matricule', 'password']))) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }


    $token=$user->createToken('myapptoken')->plainTextToken;
    $response = [
        'name' => $user->name,
        'id'=>$user->id,
        'role' => $user->role->name,
        'token'=> $token
    ];
    return response($response,201);

}
  public function logout(Request $request){
    $request->user()->currentAccessToken()->delete();
    return response()->json(['success' => true,], 200);
}
public function updatePassword(Request $request)
{
    $token = DB::table('personal_access_tokens')->where("id", explode("|", $request->bearerToken())[0])->first();
    $user = User::find($token->tokenable_id);

    if (Hash::check($request->oldpassword, $user->password)) {
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['success' => true, "message" => "Password updated successfully"], 200);
    } else {
        return response()->json(['success' => false, "message" => "Incorrect old password"], 401);
    }
}
//



public function user()
{
    $user = Auth::user();

switch($user->role_id){
    case '1': // role id = 1 => chef unité
        $structure = Unite::where('UCM_ID', $user->structure_id)->value('UCM_LIB');
        $formattedDate = $user->created_at->format('Y-m-d');
        $data = [
            'name' => $user->name,
            'matricule' => $user->matricule,
            'email' => $user->email,
            'role' => "Chef d'unité",
            'structure' => $structure,
            'structure_id' => $user->structure_id,
            'created_at' => $formattedDate,
        ];
        break;
        case '2':
        $structure = Centre::where('COP_ID', $user->structure_id)->value('COP_LIB');
        $formattedDate = $user->created_at->format('Y-m-d');
        $data = [
            'name' => $user->name,
            'matricule' => $user->matricule,
            'email' => $user->email,
            'role' => "Chef de centre",
            'structure' => $structure,
            'structure_id' => $user->structure_id,
            'created_at' => $formattedDate,
        ];
        break;
 case '3': // role id = 3 => chef equipe
    $structure = Centre::where('COP_ID', $user->structure_id)->value('COP_LIB');
        $formattedDate = $user->created_at->format('Y-m-d');
        $data = [
            'name' => $user->name,
            'matricule' => $user->matricule,
            'email' => $user->email,
            'role' => "Chef d'équipe",
            'structure' => $structure,
            'structure_id' => $user->structure_id,
            'created_at' => $formattedDate,
        ];
}
    return $data;
}}
