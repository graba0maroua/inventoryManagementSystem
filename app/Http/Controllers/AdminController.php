<?php

namespace App\Http\Controllers;

use App\Models\DemandeCompte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function acceptDemandeCompte(string $id)
    {
        $demandeCompte = DemandeCompte::find($id);
        if (!$demandeCompte) {
            return response()->json(['message' => 'DemandeCompte not found.'], 404);
        }
        $demandeCompte->status = 'accepted';
        $demandeCompte->edited_by = Auth::user()->name;
        $demandeCompte->save();

        $user = $demandeCompte->user;
        $user->Compte_isActivated = 1;
        $user->save();

        return response()->json(['message' => 'Demande accepted'], 200);
    }

    public function refuseDemandeCompte(string $id)
    {
        $demandeCompte = DemandeCompte::find($id);
        if (!$demandeCompte) {
            return response()->json(['message' => 'DemandeCompte not found.'], 404);
        }

        $demandeCompte->status = 'refused';
        $demandeCompte->edited_by = Auth::user()->name;
        $demandeCompte->save();

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
