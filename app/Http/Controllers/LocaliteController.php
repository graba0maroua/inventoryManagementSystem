<?php

namespace App\Http\Controllers;

use App\Models\BiensScannes;
use App\Models\Equipe;
use App\Models\Localite;
use Auth;
use DB;
class LocaliteController extends Controller{


function NotVisited_Localites()
{
    $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    // Get the equipe with the specified ID
    $groupId = Equipe::where('EMP_ID', $user->matricule)
    ->where('EMP_IS_MANAGER', 1)
     ->value('GROUPE_ID');
 // Get the equipe's assigned localities
    $localities = DB::table('equipe_localite')
                    ->where('GROUPE_ID', $groupId)
                    ->where('COP_ID', $user->structure_id)
                    ->pluck('LOC_ID');
    $localitie = Localite::whereIn('LOC_ID', $localities)
                    ->pluck('LOC_ID', 'LOC_LIB')
                    ->toArray();




    // Get the visited localities for the equipe
    $visitedLocalities = DB::table('INV.T_BIENS_SCANNES')
                            ->whereIn('LOC_ID', $localitie)
                            ->where('GROUPE_ID',$groupId)
                            ->select('LOC_ID','LOC_LIB')
                            ->distinct()
                            ->pluck('LOC_ID','LOC_LIB')
                            ->toArray();

    // Get the unvisited localities for the equipe
    $unvisitedLocalities = array_diff_key($localitie, $visitedLocalities);
    $formattedData = [];
    foreach ($unvisitedLocalities as $locLib => $locId) {
        $formattedData[] = [
            'LOC_LIB' => $locLib,
            'LOC_ID' => $locId
        ];
    }
    // Return unvisited localities
    return  $formattedData;
}
function localiteVisite()
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Get the equipe with the specified ID
    $groupId = Equipe::where('EMP_ID', $user->matricule)
        ->where('EMP_IS_MANAGER', 1)
        ->value('GROUPE_ID');

    // Get the equipe's assigned localities
    $localities = DB::table('equipe_localite')
        ->where('GROUPE_ID', $groupId)
        ->where('COP_ID', $user->structure_id)
        ->pluck('LOC_ID')
        ->toArray();

    // Get the visited localities for the equipe
    $visitedLocalities = DB::table('INV.T_BIENS_SCANNES')
        ->whereIn('LOC_ID', $localities)
        ->where('GROUPE_ID', $groupId)
        ->select('LOC_ID', 'LOC_LIB')
        ->distinct()
        ->get()
        ->toArray();

    $formattedData = [];
    foreach ($visitedLocalities as $locality) {
        $formattedData[] = [
            'LOC_LIB' => $locality->LOC_LIB,
            'LOC_ID' => $locality->LOC_ID
        ];
    }

    // Return the visited localities in the desired format
    return response()->json($formattedData);
}

}




