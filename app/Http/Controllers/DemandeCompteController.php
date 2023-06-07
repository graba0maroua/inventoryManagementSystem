<?php

namespace App\Http\Controllers;

use App\Mail\NewCompte;
use App\Mail\NewDemandeCompte;
use App\Models\DemandeCompte;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Nette\Utils\DateTime;

class DemandeCompteController extends Controller
{
    function getDemandes(Request $request) {
        $demandeComptes = DemandeCompte::select('demande_comptes.id', 'users.name', 'users.matricule', 'users.email', 'roles.name as role', 'users.created_at', 'users.structure_id', 'demande_comptes.status')
            ->join('users', 'demande_comptes.user_id', '=', 'users.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('demande_comptes.id', 'like', '%' . $request['keyword'] . '%')
            ->orWhere('users.name', 'like', '%' . $request['keyword'] . '%')
            ->orWhere('users.matricule', 'like', '%' . $request['keyword'] . '%')
            ->orWhere('users.email', 'like', '%' . $request['keyword'] . '%')
            ->orWhere('roles.name', 'like', '%' . $request['keyword'] . '%')
            ->orWhere('users.created_at', 'like', '%' . $request['keyword'] . '%')
            ->orWhere('users.structure_id', 'like', '%' . $request['keyword'] . '%')
            ->orWhere('demande_comptes.status', 'like', '%' . $request['keyword'] . '%')
            ->get()
            ->map(function ($item) {
                $item->created_at = (new DateTime($item->created_at))->format('Y-m-d H:i');
                return $item;
            });

        $demandeComptes = $demandeComptes->toArray();

        foreach ($demandeComptes as &$demande) {
            $demande['created_at'] = (new DateTime($demande['created_at']))->format('Y-m-d H:i');
        }

        return response()->json($demandeComptes);
    }

















}
