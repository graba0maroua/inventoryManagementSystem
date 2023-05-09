<?php

namespace App\Http\Controllers;

use App\Mail\NewCompte;
use App\Mail\NewDemandeCompte;
use App\Models\DemandeCompte;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DemandeCompteController extends Controller
{
    function getDemandes(Request $request) {
        $demandeComptes = DemandeCompte::select('demande_comptes.id', 'users.name', 'users.matricule', 'users.email', 'roles.name as role', 'users.Compte_isActivated','users.structure_id', 'demande_comptes.status')
            ->join('users', 'demande_comptes.user_id', '=', 'users.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->get();

        return response()->json($demandeComptes);
    }










}
