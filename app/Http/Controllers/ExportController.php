<?php

namespace App\Http\Controllers;
use DB;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

use Illuminate\Http\Request;

class ExportController extends Controller
{
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

    // Generate PDF using the Blade template
    $pdf = new Dompdf();
    $pdf->loadHtml(View::make('exports.infrastructure-unite-pdf', compact('result')));
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();

    // Generate a unique file name
    $fileName = 'infrastructure-unite-' . date('Y-m-d-H-i-s') . '.pdf';

    // Store the PDF file in the storage directory
    $pdf->stream($fileName, ['Attachment' => true]);

    return response()->json(['message' => 'PDF exported successfully']);
}
}
