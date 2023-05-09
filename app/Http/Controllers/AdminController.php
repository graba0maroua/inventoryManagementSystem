<?php

namespace App\Http\Controllers;

use App\Models\DemandeCompte;
use App\Mail\AccountState;
use App\Mail\AccountRefused;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mail;

class AdminController extends Controller
{
    public function acceptDemandeCompte(string $id)
    {
        $demandeCompte = DemandeCompte::find($id);
        if (!$demandeCompte) {
            return response()->json(['message' => 'DemandeCompte not found.'], 404);
        }
        $demandeCompte->status = 'accepted';
        $demandeCompte->save();

        $user = $demandeCompte->user;
        $demandeCompte->edited_by = Auth::user()->name;
        $user->Compte_isActivated = 1;
        $user->save();
        Mail::to($user->email)->send(new AccountState($user));

        return response()->json(['message' => 'Demande accepted'], 200);
    }

    public function refuseDemandeCompte(string $id)
    {
        $demandeCompte = DemandeCompte::find($id);
        if (!$demandeCompte) {
            return response()->json(['message' => 'DemandeCompte not found.'], 404);
        }
        $user = $demandeCompte->user;
        $demandeCompte->status = 'refused';
        $demandeCompte->edited_by = Auth::user()->name;
        $demandeCompte->save();
        Mail::to($user->email)->send(new AccountRefused($user));

        return response()->json(['message' => 'DemandeCompte refused.'], 200);
    }

    public function deactivateUser(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->Compte_isActivated) {
            $user->Compte_isActivated = 0;
            $user->save();

            return response()->json(['message' => 'User account has been deactivated.'], 200);
        } else {
            return response()->json(['message' => 'User account is already deactivated.'], 200);
        }
    }
    public function deleteUser(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
         return response()->json(['message' => 'User account has been deleted.'], 200);
    }
    public function role()
    {
        $user = Auth::user();
        return response()->json($user);
    }



}
