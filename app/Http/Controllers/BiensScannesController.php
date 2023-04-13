<?php

namespace App\Http\Controllers;

use App\Models\BiensScannes;
use App\Models\Localite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiensScannesController extends Controller
{

    public function index()
    {
        return BiensScannes::all();
    }
// * les localités visités et non visités
    public function localitesVisites(){
        $visitedLocalities =Localite::has('biensScannes')
        ->select('LOC_ID', 'LOC_LIB')
        ->get();
        return response()->json($visitedLocalities);
    }

    public function localitesNonVisites(){
        $NotvisitedLocalities = Localite::doesntHave('biensScannes') ->select('LOC_ID', 'LOC_LIB')
        ->get();
        return response()->json($NotvisitedLocalities);
    }

//* Filtrer liste d'inventaires par structure
public function listeInventairesScannés()
{
    $user = Auth::user();
    switch ($user->role_id) {
        case '1':
            $scannedInventory = BiensScannes::where('UCM_ID', $user->structure_id)->get();
            break;
        case '2':
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
}
