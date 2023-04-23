<?php

namespace App\Http\Controllers;

use App\Models\BiensScannes;
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
// * les localités visités et non visités
// //     public function localitesVisites(){
// //         $visitedLocalities =Localite::has('biensScannes')
// //         ->select('LOC_ID', 'LOC_LIB')
// //         ->groupBy('LOC_ID','LOC_LIB')
// //         ->get();
// //         return response()->json($visitedLocalities);
// //     }
// //     public function localitesNonVisites(){
// //         $NotvisitedLocalities = Localite::doesntHave('biensScannes') ->select('LOC_ID', 'LOC_LIB')
// //         ->get();
// //         return response()->json($NotvisitedLocalities);
// //     }
//* Filtrer liste d'inventaires par structure
public function listeInventairesScannes(Request $request)
{
    $user =   Auth::user();

if (!$user) {
    return response()->json(['message' => 'User not found'], 404);
}
    switch ($user->role_id) {
        //TODO add table Unités avec ces centres
        // case '1':
        //     $scannedInventory = BiensScannes::where('UCM_ID', $user->structure_id)->get();
        //     break;
        case '2': //? TESTED 
            $scannedInventory = BiensScannes::where('COP_ID', $user->structure_id)->get();
            break;
        case '3':
            $scannedInventory = BiensScannes::whereIn('LOC_ID', function($query) use ($user) {
                        $query->select('LOC_ID')
                              ->from('BienScannés')
                              ->where('EMP_ID', $user->matricule)
                              ->where('EMP_IS_MANAGER', 1)
                              ->groupBy('LOC_ID');
                    })->get();
                    break;
}
     return response()->json(['inventoryList' => $scannedInventory], 200);
}

public function countScannedInventoryByCenter()
{
    $counts = DB::table('INV.T_R_CENTRE_OPERATIONNEL_COP')
        ->leftJoin('INV.T_BIENS_SCANNES', 'INV.T_R_CENTRE_OPERATIONNEL_COP.COP_ID', '=', 'INV.T_BIENS_SCANNES.COP_ID')
        ->select('INV.T_R_CENTRE_OPERATIONNEL_COP.COP_ID', 'INV.T_R_CENTRE_OPERATIONNEL_COP.COP_LIB', DB::raw('COUNT(DISTINCT INV.T_BIENS_SCANNES.code_bar) as scanned_count'))
        ->groupBy('INV.T_R_CENTRE_OPERATIONNEL_COP.COP_ID', 'INV.T_R_CENTRE_OPERATIONNEL_COP.COP_LIB')
        ->get();

    return response()->json($counts);
}
public function countScannedInventoryByLocality()
{
    $counts = Localite::withCount(['biensScannes as scanned_count' => function ($query) {
            $query->select(DB::raw('COUNT(DISTINCT code_bar)'));
        }])
        ->get(['LOC_ID', 'COP_ID']);

    return response()->json($counts);
}
public function countScannedInventoryByLocalite()
    {
        $localites = Localite::withCount('biensScannes')->get();

        $result = [];
        foreach ($localites as $localite) {
            $count = $localite->biens_scannes_count;
            $result[] = [
                'localite_id' => $localite->LOC_ID,
                'localite_name' => $localite->LOC_LIB,
                'count' => $count,
            ];
        }

        return $result;
    }


}
