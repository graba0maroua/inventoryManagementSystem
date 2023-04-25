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
public function listeInventairesScannes(Request $request) //! add liste non scanne 
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
$total=0;
    foreach ($localities as $locality) {
        $count = BiensScannes::where('LOC_ID', $locality->LOC_ID)
            ->where('COP_ID', $locality->COP_ID)
            ->count();
        $result[] = [
            'locality' => $locality->LOC_LIB,
            'centre'=>$locality->COP_ID,
            'count' => $count
        ];
        $total=$total+$count;
    }

    return response()->json(['localités' => $result,$total], 200);
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
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
public function getLocalitiesWithNotScannedInventoryy()//? ELEQUENT
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

public function getLocalitiesWithNotScannedInventory() //!modify join  aset mea bienScanne
{
    $localities = Localite::all();
    $result = [];

    foreach ($localities as $locality) {
        $query = DB::table('INV.T_E_LOCATION_LOC as l')
        ->join('INV.T_R_CENTRE_OPERATIONNEL_COP as c', 'l.COP_ID', '=', 'c.COP_ID')
        ->leftJoin('INV.T_E_ASSET_AST as a', function($join) use($locality) {
            $join->on('l.LOC_ID', '=', 'a.LOC_ID_INIT')
                 ->whereNotIn('a.AST_CB', function($query) use($locality) {
                     $query->select('code_bar')
                           ->from('INV.T_BIENS_SCANNES')
                           ->whereRaw('LOC_ID = a.LOC_ID_INIT')
                           ->whereRaw('COP_ID = l.COP_ID');
                 });
        })
        ->select('l.LOC_ID as locality_id', 'l.LOC_LIB as locality_name', 'c.COP_ID as centre_id', DB::raw('COUNT(a.AST_CB) as not_scanned_count'))
        ->groupBy('l.LOC_ID', 'l.LOC_LIB', 'c.COP_ID')
        ->get();

        $result[] = $query->first();
    }

    return response()->json(['localités' => $result], 200);
}

public function getLocalitiesWithNotScannedInventorySQL() //! modify left join §§§
{
    $query = "
        SELECT
          l.LOC_ID AS locality_id,
          l.LOC_LIB AS locality_name,
          l.COP_ID AS centre_id,
          COUNT(a.AST_CB) AS not_scanned_count,
          STRING_AGG(a.AST_CB, ', ') AS not_scanned_code_bars
        FROM INV.T_E_LOCATION_LOC AS l
        LEFT JOIN INV.T_E_ASSET_AST AS a
          ON l.LOC_ID = a.LOC_ID_INIT
        LEFT JOIN INV.T_BIENS_SCANNES AS b
          ON b.code_bar = a.AST_CB AND b.LOC_ID = l.LOC_ID AND b.COP_ID = l.COP_ID
        WHERE b.INV_ID IS NULL
        GROUP BY l.LOC_ID, l.LOC_LIB, l.COP_ID
    ";

    $result = DB::select($query);

    return response()->json(['localités' => $result], 200);
}

}
