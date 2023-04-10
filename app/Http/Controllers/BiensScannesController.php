<?php

namespace App\Http\Controllers;

use App\Models\BiensScannes;
use App\Models\Localite;
use Illuminate\Http\Request;

class BiensScannesController extends Controller
{

    public function index()
    {
        return BiensScannes::all();
    }

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


}
