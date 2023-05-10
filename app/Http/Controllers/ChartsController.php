<?php

namespace App\Http\Controllers;

use App\Models\Assets;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChartsController extends Controller
{
    public function lineChart()
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
}

return $result;
    }}}
