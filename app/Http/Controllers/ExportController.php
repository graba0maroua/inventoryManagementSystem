<?php

namespace App\Http\Controllers;
use App\Models\Unite;
use DB;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class ExportController extends Controller
{
    public function infrastructureUnitepdf()
{
    $results = DB::select("
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

    foreach ($results as $result) {
        if ($result->total_count == 0) {
            $result->percentage = 0;
        } else {
            $num = ($result->scanned_count / $result->total_count) * 100;
            $result->percentage = round($num);
        }
    }

    // Generate PDF using the Blade template
    $pdf = new Dompdf();
    $pdf->loadHtml(View::make('infrastructure-unite-pdf', ['results' => $results]));
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();

    // Generate a unique file name
    $fileName = 'infrastructure-unite-' . date('Y-m-d-H-i-s') . '.pdf';

    // Store the PDF file in the storage directory
    $pdf->stream($fileName, ['Attachment' => true]);

    return response()->json(['message' => 'PDF exported successfully']);
}

public function infrastructureUniteExcel()
{
    $results = Unite::leftJoin('INV.T_R_CENTRE_OPERATIONNEL_COP as c', 'INV.T_R_UNITE_COMPTABLE_UCM.UCM_ID', '=', 'c.UCM_ID')
        ->leftJoin('INV.T_E_ASSET_AST as a', 'c.COP_ID', '=', 'a.COP_ID')
        ->leftJoin('INV.T_BIENS_SCANNES as b', function ($join) {
            $join->on('b.code_bar', '=', 'a.AST_CB');
            $join->on('b.COP_ID', '=', 'c.COP_ID');
        })
        ->leftJoin(
            DB::raw('(SELECT u.UCM_ID, COUNT(DISTINCT AST_CB) AS not_scanned_count
                FROM INV.T_R_UNITE_COMPTABLE_UCM u
                LEFT JOIN INV.T_R_CENTRE_OPERATIONNEL_COP c ON u.UCM_ID = c.UCM_ID
                LEFT JOIN INV.T_E_ASSET_AST a ON c.COP_ID = a.COP_ID
                WHERE a.AST_CB NOT IN (
                    SELECT code_bar FROM INV.T_BIENS_SCANNES WHERE COP_ID = c.COP_ID
                )
                GROUP BY u.UCM_ID) AS c2'),
            'INV.T_R_UNITE_COMPTABLE_UCM.UCM_ID',
            '=',
            'c2.UCM_ID'
        )
        ->groupBy('INV.T_R_UNITE_COMPTABLE_UCM.UCM_ID', 'INV.T_R_UNITE_COMPTABLE_UCM.UCM_LIB')
        ->select(
            'INV.T_R_UNITE_COMPTABLE_UCM.UCM_ID as unit_id',
            'INV.T_R_UNITE_COMPTABLE_UCM.UCM_LIB as unit_name',
            DB::raw('COUNT(DISTINCT INV.T_E_ASSET_AST.AST_CB) as total_count'),
            DB::raw('COUNT(DISTINCT INV.T_BIENS_SCANNES.code_bar) as scanned_count'),
            DB::raw('COUNT(DISTINCT INV.T_E_ASSET_AST.AST_CB) - COUNT(DISTINCT INV.T_BIENS_SCANNES.code_bar) as not_scanned_count')
        )
        ->get();

    foreach ($results as $result) {
        if ($result->total_count == 0) {
            $result->percentage = 0;
        } else {
            $num = ($result->scanned_count / $result->total_count) * 100;
            $result->percentage = round($num);
        }
    }

    // Export the results as an Excel file
    return Excel::download(new InfrastructureUniteExport($results), 'infrastructure-unite.xlsx');
}




      }










