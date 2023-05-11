<?php

namespace App\Http\Controllers;

use App\Models\Assets;

use App\Models\BiensScannes;
use App\Models\Equipe;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChartsController extends Controller
{ public function lineChart()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        switch ($user->role_id) {
case '1': // role id = 1 => chef unité


// Create an array to store the results for each month
$result = [];
//initialize totalnotscanned count
$totalNotScannedCount=DB::select("
SELECT
    (COUNT(DISTINCT a.AST_CB) - COUNT(DISTINCT b.code_bar)) AS not_scanned_count
FROM INV.T_R_UNITE_COMPTABLE_UCM u
LEFT JOIN INV.T_R_CENTRE_OPERATIONNEL_COP c ON u.UCM_ID = c.UCM_ID
LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
LEFT JOIN (
    SELECT
        u.UCM_ID,
        COUNT(DISTINCT AST_CB) AS not_scanned_count
    FROM INV.T_R_UNITE_COMPTABLE_UCM u
    LEFT JOIN INV.T_R_CENTRE_OPERATIONNEL_COP c ON u.UCM_ID = c.UCM_ID
    LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
    WHERE a.AST_CB NOT IN (
        SELECT code_bar FROM INV.T_BIENS_SCANNES WHERE COP_ID = c.COP_ID
    )
    GROUP BY u.UCM_ID
) AS c2 ON u.UCM_ID = c2.UCM_ID
WHERE u.UCM_ID = '{$user->structure_id}'
GROUP BY u.UCM_ID
");
$notScannedCount = $totalNotScannedCount[0]->not_scanned_count;


for ($i = 0; $i < 12; $i++) {
    // Get the current date
    $date = Carbon::now();

    // Subtract $i months from the current date
    $date->subMonths($i);

    // Get the start and end dates for the current month
    $startOfMonth = $date->startOfMonth()->format('Y-m-d');
    $endOfMonth = $date->endOfMonth()->format('Y-m-d');

    // Query to calculate scanned counts for the current month
    $query = "
    SELECT
    COUNT(DISTINCT b.code_bar) AS scanned_count
FROM INV.T_R_UNITE_COMPTABLE_UCM u
LEFT JOIN INV.T_R_CENTRE_OPERATIONNEL_COP c ON u.UCM_ID = c.UCM_ID
LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
WHERE u.UCM_ID = '001'
 AND b.INV_DAY BETWEEN '$startOfMonth' AND '$endOfMonth'
GROUP BY u.UCM_ID
";

    // Execute the query
    $monthlyResult = DB::select($query);


    // Calculate the scanned count for the current month
    $scannedCount = ($monthlyResult[0]->scanned_count ?? 0);

    // Calculate the not scanned count for the current month
    $notScannedCount = $notScannedCount - $scannedCount;

    // Store the results for the current month
    $result[] = [
        'scanned_month' => $date->format('F'),
        'scanned_count' => $scannedCount,
        'not_scanned_count' => $notScannedCount
    ];

    // Update the total not scanned count for the next month
    $totalNotScannedCount = $notScannedCount;

}break;
        case '2': // role id = 2 => chef centre
            // Create an array to store the results for each month
            $result = [];

            // Initialize total not scanned count
            $totalNotScannedCount = DB::select("
                SELECT
                    (COUNT(DISTINCT a.AST_CB) - COUNT(DISTINCT b.code_bar)) AS not_scanned_count
                FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
                LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
                LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
                LEFT JOIN (
                    SELECT
                        c.COP_ID,
                        COUNT(DISTINCT AST_CB) AS not_scanned_count
                    FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
                    LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
                    WHERE a.AST_CB NOT IN (
                        SELECT code_bar FROM INV.T_BIENS_SCANNES WHERE COP_ID = c.COP_ID
                    )
                    GROUP BY c.COP_ID
                ) AS c2 ON c.COP_ID = c2.COP_ID
                WHERE c.COP_ID = '{$user->structure_id}'
                GROUP BY c.COP_ID
            ");
            $notScannedCount = $totalNotScannedCount[0]->not_scanned_count;


            for ($i = 0; $i < 12; $i++) {
                // Get the current date
                $date = Carbon::now();

                // Subtract $i months from the current date
                $date->subMonths($i);

                // Get the start and end dates for the current month
                $startOfMonth = $date->startOfMonth()->format('Y-m-d');
                $endOfMonth = $date->endOfMonth()->format('Y-m-d');

                // Query to calculate scanned counts for the current month
                $query = "
                    SELECT
                        COUNT(DISTINCT b.code_bar) AS scanned_count
                    FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
                    LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
                    LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
                    WHERE c.COP_ID = '{$user->structure_id}'
                        AND b.INV_DAY BETWEEN '$startOfMonth' AND '$endOfMonth'
                    GROUP BY c.COP_ID
                ";

                // Execute the query
                $monthlyResult = DB::select($query);


                // Calculate the scanned count for the current month
                $scannedCount = ($monthlyResult[0]->scanned_count ?? 0);


                // Calculate the not scanned count for the current month
                $notScannedCount = $notScannedCount - $scannedCount;

                                // Store the results for the current month
                                $result[] = [
                                    'scanned_month' => $date->format('F'),
                                    'scanned_count' => $scannedCount,
                                    'not_scanned_count' => $notScannedCount
                                ];

                                // Update the total not scanned count for the next month
                                $totalNotScannedCount = $notScannedCount;
             }break;
                        case '3': // role id = 2 => chef equipe
                            $groupId = Equipe::where('EMP_ID', $user->matricule)
                            ->where('EMP_IS_MANAGER', 1)
                            ->value('GROUPE_ID');

                            $result = DB::select("
                            SELECT
                                COUNT(CASE WHEN b.code_bar IS NULL THEN 1 END) AS not_scanned_count
                            FROM dbo.equipe_localite el
                            INNER JOIN INV.T_E_ASSET_AST a ON el.LOC_ID = a.LOC_ID_INIT AND el.COP_ID = a.COP_ID
                            LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.LOC_ID = a.LOC_ID_INIT AND b.COP_ID = a.COP_ID
                            WHERE el.COP_ID = '{$user->structure_id}' AND el.GROUPE_ID = $groupId
                        ");

                        $totalNotScannedCount = $result[0]->not_scanned_count;

                        // Create an array to store the results
                        $monthlyResult = [];

                        for ($i = 0; $i < 12; $i++) {
                            // Get the current date
                            $date = Carbon::now();

                            // Subtract $i months from the current date
                            $date->subMonths($i);

                            // Get the start and end dates for the current month
                            $startOfMonth = $date->startOfMonth()->format('Y-m-d');
                            $endOfMonth = $date->endOfMonth()->format('Y-m-d');

                            // Query to calculate scanned counts for the current month
                            $query = "
                                SELECT
                                    COUNT(DISTINCT b.code_bar) AS scanned_count
                                FROM dbo.equipe_localite el
                                INNER JOIN INV.T_E_ASSET_AST a ON el.LOC_ID = a.LOC_ID_INIT AND el.COP_ID = a.COP_ID
                                LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.LOC_ID = a.LOC_ID_INIT AND b.COP_ID = a.COP_ID
                                WHERE el.COP_ID = '{$user->structure_id}' AND el.GROUPE_ID = $groupId
                                    AND b.INV_DAY BETWEEN '$startOfMonth' AND '$endOfMonth'
                            ";

                            // Execute the query
                            $monthlyScannedCount = DB::select($query);
                            $scannedCount = $monthlyScannedCount[0]->scanned_count;

                            // Calculate the not scanned count for the current month
                            $notScannedCount = $totalNotScannedCount - $scannedCount;

                            // Store the results for the current month
                            $result[] = [
                                'scanned_month' => $date->format('F'),
                                'scanned_count' => $scannedCount,
                                'not_scanned_count' => $notScannedCount
                            ];

                            // Update the total not scanned count for the next month
                            $totalNotScannedCount = $notScannedCount;
         }break;
                        default:
                            return response()->json(['message' => 'Invalid user role'], 400);
                    }
                    return $result;
                }

public function PieChart(){
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    switch ($user->role_id) {
case '1': // role id = 1 => chef unité
     // Retrieve the data for the pie chart
     $data = DB::select("
     SELECT
         c.COP_ID AS center_id,
         c.COP_LIB AS center_name,
         COUNT(DISTINCT b.code_bar) AS scanned_count
     FROM INV.T_R_UNITE_COMPTABLE_UCM u
     LEFT JOIN INV.T_R_CENTRE_OPERATIONNEL_COP c ON u.UCM_ID = c.UCM_ID
     LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
     LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
     WHERE u.UCM_ID = '{$user->structure_id}'
     GROUP BY c.COP_ID, c.COP_LIB
 ");
 break;
 case '2':
     // Retrieve all GROUPE_IDs in the user's center
     $groupeIds = Equipe::where('COP_ID', $user->structure_id)->pluck('GROUPE_ID');


    // Retrieve the count of scanned inventory for each team
    $result = DB::table('INV.T_BIENS_SCANNES')
    ->whereIn('GROUPE_ID', $groupeIds)
    ->where('COP_ID', $user->structure_id)
    ->whereIn('code_bar', function ($query) {
        $query->select('AST_CB')
              ->from('INV.T_E_ASSET_AST');
    })
    ->select('GROUPE_ID', DB::raw('COUNT(DISTINCT code_bar) as scanned_count'))
    ->groupBy('GROUPE_ID')
    ->get();

     // Prepare the response
     $data = [];
     foreach ($result as $row) {
         $data[] = [
             'groupe_id' => $row->GROUPE_ID,
             'scanned_count' => $row->scanned_count,
         ];
     }

break;
 case '3': // role id = 3 => chef equipe
    $groupId = Equipe::where('EMP_ID', $user->matricule)
                ->where('EMP_IS_MANAGER', 1)
            ->value('GROUPE_ID');
// Retrieve the scanned inventory count for each team member
$result = DB::select("
SELECT
    b.EMP_ID,
    b.EMP_FULLNAME,
    COUNT(DISTINCT b.code_bar) AS scanned_count
FROM dbo.equipe_localite el
INNER JOIN INV.T_E_ASSET_AST a ON el.LOC_ID = a.LOC_ID_INIT AND el.COP_ID = a.COP_ID
INNER JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.LOC_ID = a.LOC_ID_INIT AND b.COP_ID = a.COP_ID
WHERE el.COP_ID = '{$user->structure_id}' AND el.GROUPE_ID = $groupId
GROUP BY b.EMP_ID, b.EMP_FULLNAME
");

// Prepare the data for the pie chart
$data = [];
foreach ($result as $row) {
$data[] = [
    'EMP_ID' => $row->EMP_ID,
    'EMP_FULLNAME' => $row->EMP_FULLNAME,
    'scanned_count' => $row->scanned_count,
];
}

    } return response()->json($data);
}
public function ProgressChart(){
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    switch ($user->role_id) {
case '1': // role id = 1 => chef unité
    $data = DB::select("
    SELECT
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
    WHERE u.UCM_ID = '{$user->structure_id}'
");
    break;
    case'2':
        $data = DB::select("
        SELECT COUNT(DISTINCT a.AST_CB) AS total_count,
        COUNT(DISTINCT b.code_bar) AS scanned_count,
        COUNT(DISTINCT a.AST_CB) - COUNT(DISTINCT b.code_bar) AS not_scanned_count
        FROM INV.T_R_CENTRE_OPERATIONNEL_COP c
        LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
        LEFT JOIN INV.T_BIENS_SCANNES b ON b.code_bar = a.AST_CB AND b.COP_ID = c.COP_ID
        WHERE c.COP_ID = '{$user->structure_id}'
    ");
        break;
        case '3': // role id = 3 => chef équipe
            $data = DB::select("
                SELECT
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
                WHERE a.LOC_ID_INIT IS NOT NULL AND l.COP_ID = '{$user->structure_id}'
            ");
}return response()->json($data);

}

}

