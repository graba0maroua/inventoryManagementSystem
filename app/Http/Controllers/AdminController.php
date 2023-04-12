<?php

namespace App\Http\Controllers;

use App\Models\DemandeCompte;
use App\Models\User;
use Illuminate\Http\Request;

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
        $user->Compte_isActivated = true;
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
        $demandeCompte->save();

        return response()->json(['message' => 'DemandeCompte refused.'], 200);
    }

    public function deactivateUser(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->Compte_isActivated) {
            $user->Compte_isActivated = false;
            $user->save();

            return response()->json(['message' => 'User account has been deactivated.'], 200);
        } else {
            return response()->json(['message' => 'User account is already deactivated.'], 200);
        }
    }
    //! MOH => i Added desactuvate and delete user but am not sure its useful
    public function deleteUser(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
         return response()->json(['message' => 'User account has been deleted.'], 200);
    }

}
