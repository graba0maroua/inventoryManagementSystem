<?php

namespace App\Http\Controllers;

use App\Models\BiensScannes;
use App\Models\Centre;
use App\Models\Equipe;
use App\Models\Localite;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use DB ;
class BiensScannesController extends Controller
{

    public function index()
    {
        return BiensScannes::all();
    }
//* Filtrer liste d'inventaires par structure
public function listeInventairesScannes(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    switch ($user->role_id) {
        case '1': // role id = 1 => chef unité
            // Get all centers for user's unit
            $centers = Centre::where('UCM_ID', $user->structure_id)->get();
            $copIds = $centers->pluck('COP_ID');
            // Get scanned inventory for user's centers
            $scannedInventory = BiensScannes::whereIn('COP_ID', $copIds)->get();
            break;
        case '2': //role id = 2 => chef centre
            $scannedInventory = BiensScannes::where('COP_ID', $user->structure_id)->get();
            break;
        case '3'://role id = 3 => chef equipe
            $scannedInventory = BiensScannes::whereIn('LOC_ID', function($query) use ($user) {
                $query->select('LOC_ID')
                    ->from('INV.T_BIENS_SCANNES')
                    ->where('EMP_ID', $user->matricule)
                    ->where('EMP_IS_MANAGER', 1)
                    ->groupBy('LOC_ID');
            })->whereIn('COP_ID', function($query) use ($user) {
                $query->select('COP_ID')
                    ->from('T_EQUIPE')
                    ->where('EMP_ID', $user->matricule)
                    ->where('EMP_IS_MANAGER', 1)
                    ->groupBy('COP_ID', 'GROUPE_ID');
            })->whereIn('GROUPE_ID', function($query) use ($user) {
                $query->select('GROUPE_ID')
                    ->from('T_EQUIPE')
                    ->where('EMP_ID', $user->matricule)
                    ->where('EMP_IS_MANAGER', 1)
                    ->groupBy('COP_ID', 'GROUPE_ID');
            })->get();
            break;
    }
    // we are checking if the user is a team head (EMP_IS_MANAGER = 1) and retrieving the GROUPE_ID and COP_ID values from the Equipe table.
    // We are then using these values to filter the scanned inventory list from the BiensScannes table.
    //  We are also checking if the user is a team head for the same COP_ID and GROUPE_ID combination in the Equipe table
    if ($scannedInventory->isEmpty()) {
        return response()->json(['message' => 'No scanned inventory found'], 404);
    }
    return response()->json(['inventoryList' => $scannedInventory], 200);
}



}
