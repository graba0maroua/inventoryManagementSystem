<?php

namespace App\Http\Controllers;

use App\Models\DemandeCompte;
use Illuminate\Http\Request;

class DemandeCompteController extends Controller
{
    function getDemandes(Request $request) {
        $demandeComptes = DemandeCompte::select('demande_comptes.id', 'users.name', 'users.matricule', 'users.email', 'roles.name as role', 'users.Compte_isActivated','users.structure_id', 'demande_comptes.status','demande_comptes.edited_by')
            ->join('users', 'demande_comptes.user_id', '=', 'users.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->get();

        return response()->json($demandeComptes);
    }










}
