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
//* Filtrer liste d'inventaires par structurepublic
public function inventoryList()
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }
    switch ($user->role_id) {
        case '1': // role id = 1 => chef unité
    $result = DB::select("
    SELECT
    a.COP_ID AS COP_ID,
 a.AST_ID AS AST_ID,
 a.AST_CB AS code_bar,
 a.AST_LIB AS AST_LIB,
 a.AST_VALBASE AS AST_VALBASE,
 a.AST_DTE_ACQ AS AST_DTE_ACQ,
 a.LOC_ID_INIT AS LOC_ID_INIT,
 a.LOC_LIB_INIT AS LOC_LIB_INIT,
             CASE
                 WHEN b.code_bar IS NOT NULL THEN 'Scanned'
                 ELSE 'Not Scanned'
             END AS status
         FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
         LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
         LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
        WHERE c.UCM_ID = " . $user->structure_id
    );
break;
case '2': //role id = 2 => chef centre
    $result = DB::select("
                SELECT
                    a.COP_ID AS COP_ID,
                    a.AST_ID AS AST_ID,
                    a.AST_CB AS code_bar,
                    a.AST_LIB AS AST_LIB,
                    a.AST_VALBASE AS AST_VALBASE,
                    a.AST_DTE_ACQ AS AST_DTE_ACQ,
                    a.LOC_ID_INIT AS LOC_ID_INIT,
                    a.LOC_LIB_INIT AS LOC_LIB_INIT,
                    CASE
                        WHEN b.code_bar IS NOT NULL THEN 'Scanned'
                        ELSE 'Not Scanned'
                    END AS status
                FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
                LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
                LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
                WHERE c.COP_ID = " . $user->structure_id
            );
            break;
}

    return response()->json($result);
}



// public function listeInventairesScanness(Request $request)
// {
//     $user = Auth::user();
//     if (!$user) {
//         return response()->json(['message' => 'User not found'], 404);
//     }

//     switch ($user->role_id) {
//         case '1': // role id = 1 => chef unité
//             // Get all centers for user's unit
//             $centers = Centre::where('UCM_ID', $user->structure_id)->get();
//             $copIds = $centers->pluck('COP_ID');
//             // Get scanned inventory for user's centers
//             $scannedInventory = BiensScannes::whereIn('COP_ID', $copIds)->pluck('code_bar')->toArray();
//             // Get non-scanned inventory for user's centers
//             $nonScannedInventory = Assets::whereNotIn('code_bar', $scannedInventory)->whereIn('COP_ID', $copIds)->get();
//             break;
//         case '2': //role id = 2 => chef centre
//             $scannedInventory = BiensScannes::where('COP_ID', $user->structure_id)->pluck('INV_ID')->toArray();
//             $nonScannedInventory = Assets::whereNotIn('INV_ID', $scannedInventory)->where('COP_ID', $user->structure_id)->get();
//             break;

//         }
//         return response()->json(['ScannedinventoryList' => $scannedInventory,'NotScannedinventoryList' => $nonScannedInventory], 200);}



// public function listeInventairesScannes(Request $request) //! add liste non scanne
// {
//     $user = Auth::user();
//     if (!$user) {
//         return response()->json(['message' => 'User not found'], 404);
//     }

//     switch ($user->role_id) {
//         case '1': // role id = 1 => chef unité
//             // Get all centers for user's unit
//             $centers = Centre::where('UCM_ID', $user->structure_id)->get();
//             $copIds = $centers->pluck('COP_ID');
//             // Get scanned inventory for user's centers
//             $scannedInventory = BiensScannes::whereIn('COP_ID', $copIds)->get();
//             break;
//         case '2': //role id = 2 => chef centre
//             $scannedInventory = BiensScannes::where('COP_ID', $user->structure_id)->get();
//             break;
//         case '3'://role id = 3 => chef equipe
//             $scannedInventory = BiensScannes::whereIn('LOC_ID', function($query) use ($user) {
//                 $query->select('LOC_ID')
//                     ->from('INV.T_BIENS_SCANNES')
//                     ->where('EMP_ID', $user->matricule)
//                     ->where('EMP_IS_MANAGER', 1)
//                     ->groupBy('LOC_ID');
//             })->whereIn('COP_ID', function($query) use ($user) {
//                 $query->select('COP_ID')
//                     ->from('T_EQUIPE')
//                     ->where('EMP_ID', $user->matricule)
//                     ->where('EMP_IS_MANAGER', 1)
//                     ->groupBy('COP_ID', 'GROUPE_ID');
//             })->whereIn('GROUPE_ID', function($query) use ($user) {
//                 $query->select('GROUPE_ID')
//                     ->from('T_EQUIPE')
//                     ->where('EMP_ID', $user->matricule)
//                     ->where('EMP_IS_MANAGER', 1)
//                     ->groupBy('COP_ID', 'GROUPE_ID');
//             })->get();
//             break;
//     }
//     // we are checking if the user is a team head (EMP_IS_MANAGER = 1) and retrieving the GROUPE_ID and COP_ID values from the Equipe table.
//     // We are then using these values to filter the scanned inventory list from the BiensScannes table.
//     //  We are also checking if the user is a team head for the same COP_ID and GROUPE_ID combination in the Equipe table
//     if ($scannedInventory->isEmpty()) {
//         return response()->json(['message' => 'No scanned inventory found'], 404);
//     }
//     return response()->json(['inventoryList' => $scannedInventory], 200);
// }
// // *TESTED
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
// public function getLocalitiesWithNotScannedInventoryy()//? ELEQUENT
// {
//     $localities = Localite::all();
//     $result = [];

//     foreach ($localities as $locality) {
//     $notScannedCount = Assets::where('COP_ID', $locality->COP_ID)
//             ->whereNotIn('AST_CB', function($query) use ($locality) {
//                 $query->select('code_bar')
//                     ->from('INV.T_BIENS_SCANNES')
//                     ->where('COP_ID', $locality->COP_ID)
//                     ->where('LOC_ID', $locality->LOC_ID);
//             })
//             ->count();
//         $result[] = [
//             'locality' => $locality->LOC_LIB,
//             'centre' => $locality->COP_ID,
//             'not_scanned_count' => $notScannedCount
//         ];
//     }

//     return response()->json(['localités' => $result], 200);
// }

// public function getLocalitiesWithNotScannedInventorySQL() //! modify left join §§§
// {
//     $notScannedAssets = Assets::select(DB::raw('
//     LOC_ID_INIT AS locality_id,
//     LOC_LIB_INIT AS locality_name,
//     COP_ID AS centre_id,
//     COUNT(*) AS not_scanned_count,
//     STRING_AGG(AST_ID, \', \') AS not_scanned_asset_ids
// '))
// ->whereNotIn('AST_CB', function ($query) {
//     $query->select('code_bar')
//         ->from('INV.T_BIENS_SCANNES');
// })
// ->groupBy('LOC_ID_INIT', 'LOC_LIB_INIT', 'COP_ID')
// ->get();

// $jsonResult = $notScannedAssets->toJson();

// return $jsonResult;



public function getLocalitiesWithNotScannedInventorySQL() {
    $query = "
    SELECT
    LOC_ID_INIT AS locality_id,
    LOC_LIB_INIT AS locality_name,
    COP_ID AS centre_id,
    COUNT(*) AS not_scanned_count,
    STUFF(
        (SELECT ', ' + CAST(AST_ID AS VARCHAR(MAX))
         FROM INV.T_E_ASSET_AST
         WHERE AST_CB NOT IN (SELECT code_bar FROM INV.T_BIENS_SCANNES)
         AND LOC_ID_INIT = a.LOC_ID_INIT
         AND COP_ID = a.COP_ID
         FOR XML PATH ('')),
         1,
         2,
         ''
     ) AS not_scanned_asset_ids
FROM INV.T_E_ASSET_AST AS a
WHERE AST_CB NOT IN (SELECT code_bar FROM INV.T_BIENS_SCANNES)
GROUP BY LOC_ID_INIT, LOC_LIB_INIT, COP_ID;

    ";

    $result = DB::select($query);

    return response()->json(['localités' => $result], 200);
}
public function getLocalitiesWithNotScannedInventorySQL2() {
    $query = "
    SELECT
    LOC_ID_INIT AS locality_id,
    LOC_LIB_INIT AS locality_name,
    COP_ID AS centre_id,
    COUNT(*) AS not_scanned_count
FROM INV.T_E_ASSET_AST AS a
WHERE AST_CB NOT IN (SELECT code_bar FROM INV.T_BIENS_SCANNES)
GROUP BY LOC_ID_INIT, LOC_LIB_INIT, COP_ID;

    ";
    $result = DB::select($query);

    return response()->json(['localités' => $result], 200);
}
// * INFRASTRUCTURE : CENTRE
public function infrastructureCentre()
{
    $result = DB::select("
    SELECT c.COP_ID AS center_id, c.COP_LIB AS center_name,
    COUNT(DISTINCT a.AST_CB) AS total_count,
    COUNT(DISTINCT b.code_bar) AS scanned_count,
    COUNT(DISTINCT a.AST_CB) - COUNT(DISTINCT b.code_bar) AS not_scanned_count
FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
LEFT JOIN (
 SELECT COP_ID, COUNT(DISTINCT AST_CB) AS not_scanned_count
 FROM INV.T_E_ASSET_AST
 WHERE AST_CB NOT IN (
     SELECT code_bar FROM INV.T_BIENS_SCANNES WHERE COP_ID = T_E_ASSET_AST.COP_ID
 )
 GROUP BY COP_ID
) AS c2 ON c.COP_ID = c2.COP_ID
GROUP BY c.COP_ID, c.COP_LIB
    ");

    foreach ($result as $row) {
        if ($row->total_count == 0) {
            $row->percentage = 0;
        } else {
            $num = ($row->scanned_count / $row->total_count) * 100;
            if (($num * 10) % 10 <= 5) {
              $row->percentage = floor($num);
            } else {
              $row->percentage = ceil($num);
            }
        }
    }

    return response()->json($result);
}
public function infrastructureLocalite()
{
    $result = DB::select("
    SELECT
    l.LOC_ID AS locality_id,
    l.LOC_LIB AS locality_name,
    COUNT(DISTINCT a.AST_CB) AS total_count,
    COUNT(DISTINCT b.code_bar) AS scanned_count,
    COUNT(DISTINCT a.AST_CB) - COUNT(DISTINCT b.code_bar) AS not_scanned_count
FROM INV.T_E_LOCATION_LOC l
LEFT JOIN INV.T_E_ASSET_AST a ON l.LOC_ID = a.LOC_ID_INIT
LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.LOC_ID = a.LOC_ID_INIT
LEFT JOIN (
    SELECT l.LOC_ID, COUNT(DISTINCT AST_CB) AS not_scanned_count
    FROM INV.T_E_LOCATION_LOC l
    LEFT JOIN INV.T_E_ASSET_AST a ON l.LOC_ID = a.LOC_ID_INIT
    WHERE a.AST_CB NOT IN (
        SELECT code_bar FROM INV.T_BIENS_SCANNES WHERE LOC_ID = l.LOC_ID
    )
    GROUP BY l.LOC_ID
) AS l2 ON l.LOC_ID = l2.LOC_ID
WHERE a.LOC_ID_INIT IS NOT NULL
GROUP BY l.LOC_ID, l.LOC_LIB
    ");

    foreach ($result as $row) {
        if ($row->total_count == 0) {
            $row->percentage = 0;
        } else {
            $num = ($row->scanned_count / $row->total_count) * 100;
            if (($num * 10) % 10 <= 5) {
              $row->percentage = floor($num);
            } else {
              $row->percentage = ceil($num);
            }
        }
    }

    return response()->json($result);
}
public function infrastructureUnite()
{
    $result = DB::select("
    SELECT
    u.UCM_ID AS unit_id,
    u.UCM_LIB AS unit_name,
    COUNT(DISTINCT a.AST_CB) AS total_count,
    COUNT(DISTINCT b.code_bar) AS scanned_count,
    COUNT(DISTINCT a.AST_CB) - COUNT(DISTINCT b.code_bar) AS not_scanned_count
   FROM INV.T_R_UNITE_COMPTABLE_UCM u
LEFT JOIN INV.T_R_CENTRE_OPERATIONNEL_COP c ON u.UCM_ID = c.UCM_ID
LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
LEFT JOIN (
    SELECT u.UCM_ID, COUNT(DISTINCT AST_CB) AS not_scanned_count
    FROM INV.T_R_UNITE_COMPTABLE_UCM u
    LEFT JOIN INV.T_R_CENTRE_OPERATIONNEL_COP c ON u.UCM_ID = c.UCM_ID
    LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
    WHERE a.AST_CB NOT IN (
        SELECT code_bar FROM INV.T_BIENS_SCANNES WHERE COP_ID = c.COP_ID
    )
    GROUP BY u.UCM_ID
) AS c2 ON u.UCM_ID = c2.UCM_ID
GROUP BY u.UCM_ID, u.UCM_LIB
    ");

    foreach ($result as $row) {
        if ($row->total_count == 0) {
            $row->percentage = 0;
        } else {
            $num = ($row->scanned_count / $row->total_count) * 100;
            if (($num * 10) % 10 <= 5) {
              $row->percentage = floor($num);
            } else {
              $row->percentage = ceil($num);
            }
        }
    }

    return response()->json($result);
}

// Here, we're selecting the unit ID and name from the T_R_UNITE_COMPTABLE_UCM table,
// as well as the total count of assets (DISTINCT AST_CB) in that unit from the T_E_ASSET_AST table,
//  the count of scanned assets (DISTINCT code_bar) from the T_BIENS_SCANNES table, and the count of assets that have not been scanned (which is the difference between the total count and the scanned count).
// T_R_UNITE_COMPTABLE_UCM (u): This table contains information about the accounting units.
// T_R_CENTRE_OPERATIONNEL_COP (c): This table contains information about the operational centers, including their corresponding accounting units.
// T_E_ASSET_AST (a): This table contains information about the assets, including their corresponding operational centers.
// T_BIENS_SCANNES (b): This table contains information about the assets that have been scanned, including their corresponding operational centers.
// The LEFT JOIN keyword is used to join the tables, which means that all rows from the left table (in this case, u) will be included in the result set, regardless of whether there is a match in the right table (in this case, c).

// We're joining the tables on the following columns:

// u.UCM_ID = c.UCM_ID: This links the accounting units in T_R_UNITE_COMPTABLE_UCM with the corresponding operational centers in T_R_CENTRE_OPERATIONNEL_COP.
// c.COP_ID = a.COP_ID: This links the operational centers in T_R_CENTRE_OPERATIONNEL_COP with the corresponding assets in T_E_ASSET_AST.
// b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID: This links the assets in T_E_ASSET_AST with the scanned assets in T_BIENS
}
