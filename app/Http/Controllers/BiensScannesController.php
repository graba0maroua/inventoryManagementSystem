<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use App\Models\BiensScannes;
use App\Models\Centre;
use App\Models\Equipe;
use App\Models\Localite;
use App\Models\Unite;
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
// *TESTED
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
// *TESTED
public function getLocalitiesWithScannedInventory()
{
    $localities = Localite::all();
    $result = [];

    foreach ($localities as $locality) {
        $count = BiensScannes::where('LOC_ID', $locality->LOC_ID)
            ->where('COP_ID', $locality->COP_ID)
            ->count();
        $result[] = [
            'locality' => $locality->LOC_LIB,
            'centre'=>$locality->COP_ID,
            'count' => $count
        ];
    }

    return response()->json(['localités' => $result], 200);
}
//*TESTED
public function getCentersWithScannedInventory()
{
    $centers = Centre::all();
    $result = [];
    foreach ($centers as $center) {
        $count = BiensScannes::where('COP_ID', $center->COP_ID)->count();
        $result[] = [
            'center_id' => $center->COP_ID,
            'center_name' => $center->COP_LIB,
            'count' => $count
        ];

    }

    return response()->json(['centres' => $result], 200);
}
//* TESTED
public function getUnitsWithScannedInventory()
{
    $units = Unite::all();
    $result = [];

    foreach ($units as $unit) {
        $centers = Centre::where('UCM_ID', $unit->UCM_ID)->get();
        $copIds = $centers->pluck('COP_ID');

        $count = BiensScannes::whereIn('COP_ID', $copIds)->count();

        $result[] = [
            'unit' => $unit->UCM_LIB,
            'unit-id'=>$unit->UCM_ID,
            'count' => $count
        ];
    }
    return response()->json(['units' => $result], 200);
}

public function LocalitiesWithNotScannedInventory() //? SQL query
{
    $localities = Localite::all();
    $result = [];

    foreach ($localities as $locality) {
$notScannedCount = DB::select("
            SELECT COUNT(*) as count
            FROM INV.T_E_ASSET_AST a
            LEFT JOIN INV.T_BIENS_SCANNES b
            ON a.AST_CB = b.code_bar
            WHERE a.COP_ID = ?
            AND b.LOC_ID IS NULL
        ", [$locality->COP_ID])[0]->count;

    $result[] = [
            'locality' => $locality->LOC_LIB,
            'centre' => $locality->COP_ID,

            'not_scanned_count' => $notScannedCount,

        ];
    }

    return response()->json(['localités' => $result], 200);
}
public function getLocalitiesWithNotScannedInventory() //? elequent
{
    $localities = Localite::all();
    $result = [];

    foreach ($localities as $locality) {
    $notScannedCount = Assets::where('COP_ID', $locality->COP_ID)
            ->whereNotIn('AST_CB', function($query) use ($locality) {
                $query->select('code_bar')
                    ->from('INV.T_BIENS_SCANNES')
                    ->where('COP_ID', $locality->COP_ID)
                    ->where('LOC_ID', $locality->LOC_ID);
            })
            ->count();
        $result[] = [
            'locality' => $locality->LOC_LIB,
            'centre' => $locality->COP_ID,
            'not_scanned_count' => $notScannedCount
        ];
    }

    return response()->json(['localités' => $result], 200);
}

public function getCentersWithNotScannedInventory() //? elequent
{
    $centers = Centre::all();
    $result = [];
    foreach ($centers as $center) {
        $notScannedCount = Assets::where('COP_ID', $center->COP_ID)
            ->whereNotIn('AST_CB', function($query) use ($center) {
                $query->select('code_bar')
                    ->from('INV.T_BIENS_SCANNES')
                    ->where('COP_ID', $center->COP_ID);
            })
            ->count();
        $result[] = [
            'center_id' => $center->COP_ID,
            'center_name' => $center->COP_LIB,
            'not_scanned_count' => $notScannedCount
        ];
    }

    return response()->json(['centres' => $result], 200);
}
// *TESTED
public function getCentersWithNotScannedInventorySQL()
{
    $result = DB::select("
        SELECT c.COP_ID AS center_id, c.COP_LIB AS center_name, COUNT(a.AST_CB) AS not_scanned_count
        FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
        LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID AND a.AST_CB NOT IN (
            SELECT b.code_bar
            FROM INV.T_BIENS_SCANNES b
            WHERE b.COP_ID = c.COP_ID
        )
        GROUP BY c.COP_ID, c.COP_LIB
    ");

    return response()->json(['centres' => $result], 200);
}
// *TESTED
public function getUnitsWithNotScannedInventorySQL()
{
    $result = DB::select("
        SELECT u.UCM_ID AS unit_id, u.UCM_LIB AS unit_name, COUNT(a.AST_CB) AS not_scanned_count
        FROM INV.T_R_UNITE_COMPTABLE_UCM u
        LEFT JOIN INV.T_R_CENTRE_OPERATIONNEL_COP c ON u.UCM_ID = c.UCM_ID
        LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID AND a.AST_CB NOT IN (
            SELECT b.code_bar
            FROM INV.T_BIENS_SCANNES b
            WHERE b.COP_ID = c.COP_ID
        )
        GROUP BY u.UCM_ID, u.UCM_LIB
    ");

    return response()->json(['units' => $result], 200);
}


}
