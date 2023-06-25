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

    public function listCentre(){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $result = DB::select("
                SELECT
                  c.COP_ID AS COP_ID,
                    a.AST_ID AS AST_ID,
                    a.AST_CB AS code_bar,
                    a.AST_LIB AS AST_LIB,
                    a.AST_VALBASE AS AST_VALBASE,
                    a.AST_DTE_ACQ AS AST_DTE_ACQ,
                    a.LOC_ID_INIT AS LOC_ID_INIT,
                    a.LOC_LIB_INIT AS LOC_LIB_INIT,
                    CASE
                        WHEN b.code_bar IS NOT NULL THEN 'Scanné'
                        ELSE 'Non Scanné'
                    END AS status
                FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
                LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
                LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
                WHERE c.COP_ID = :structure_id
                ", ['structure_id' => $user->structure_id]);
           return response()->json($result);
    }
    public function listUnite(){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
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
                 WHEN b.code_bar IS NOT NULL THEN 'Scanné'
                 ELSE 'Non Scanné'
             END AS status
         FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
         LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
         LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
        WHERE c.UCM_ID = " . $user->structure_id
    );
    return response()->json($result);
    }
    public function listLocalite(){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $groupId = Equipe::where('EMP_ID', $user->matricule)
                ->where('EMP_IS_MANAGER', 1)
            ->value('GROUPE_ID');

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
                    WHEN b.code_bar IS NOT NULL THEN 'Scanné'
                    ELSE 'Non Scanné'
                END AS status
            FROM dbo.equipe_localite el
            INNER JOIN INV.T_E_ASSET_AST a ON el.LOC_ID = a.LOC_ID_INIT AND el.COP_ID = a.COP_ID
            LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.LOC_ID = a.LOC_ID_INIT AND b.COP_ID = a.COP_ID
            WHERE el.COP_ID =$user->structure_id AND el.GROUPE_ID = $groupId
                 ");
    return response()->json($result);
    }

//* Filtrer liste d'inventaires par structure
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
                 WHEN b.code_bar IS NOT NULL THEN 'Scanné'
                 ELSE 'Non Scanné'
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
                  c.COP_ID AS COP_ID,
                    a.AST_ID AS AST_ID,
                    a.AST_CB AS code_bar,
                    a.AST_LIB AS AST_LIB,
                    a.AST_VALBASE AS AST_VALBASE,
                    a.AST_DTE_ACQ AS AST_DTE_ACQ,
                    a.LOC_ID_INIT AS LOC_ID_INIT,
                    a.LOC_LIB_INIT AS LOC_LIB_INIT,
                    CASE
                        WHEN b.code_bar IS NOT NULL THEN 'Scanné'
                        ELSE 'Non Scanné'
                    END AS status
                FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
                LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
                LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
                WHERE c.COP_ID = :structure_id
                ", ['structure_id' => $user->structure_id]);
            break;
            case '3': // role id = 3 => chef equipe
                $groupId = Equipe::where('EMP_ID', $user->matricule)
                ->where('EMP_IS_MANAGER', 1)
            ->value('GROUPE_ID');

                $result = DB::select("
                SELECT
                a.AST_ID AS AST_ID,
                a.AST_CB AS code_bar,
                a.AST_LIB AS AST_LIB,
                a.AST_VALBASE AS AST_VALBASE,
                a.AST_DTE_ACQ AS AST_DTE_ACQ,
                a.LOC_ID_INIT AS LOC_ID_INIT,
                a.LOC_LIB_INIT AS LOC_LIB_INIT,
                CASE
                    WHEN b.code_bar IS NOT NULL THEN 'Scanné'
                    ELSE 'Non Scanné'
                END AS status
            FROM dbo.equipe_localite el
            INNER JOIN INV.T_E_ASSET_AST a ON el.LOC_ID = a.LOC_ID_INIT AND el.COP_ID = a.COP_ID
            LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.LOC_ID = a.LOC_ID_INIT AND b.COP_ID = a.COP_ID
            WHERE el.COP_ID =$user->structure_id AND el.GROUPE_ID = $groupId
                 ");
                 break;
}

    return response()->json($result);
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
// * INFRASTRUCTURE : LOCALITE
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
    ");//ignore LOC_ID_INIT = NULL

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
// * INFRASTRUCTURE : UNITE
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

// we r selecting the unit ID and name from the T_R_UNITE_COMPTABLE_UCM table,
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
