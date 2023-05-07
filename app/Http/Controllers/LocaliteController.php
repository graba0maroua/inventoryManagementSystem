<?php

namespace App\Http\Controllers;

use App\Models\BiensScannes;
use App\Models\Equipe;
use Auth;
use DB;
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
                    ->pluck('LOC_ID')
                    ->toArray();

    // Get the visited localities for the equipe
    $visitedLocalities = DB::table('INV.T_BIENS_SCANNES')
                            ->whereIn('LOC_ID', $localities)
                            ->where('GROUPE_ID',$groupId)
                            ->select('LOC_ID','LOC_LIB')
                            ->distinct()
                            ->pluck('LOC_ID','LOC_LIB')
                            ->toArray();

    // Get the unvisited localities for the equipe
    $unvisitedLocalities = array_diff($localities, $visitedLocalities);

    // Return unvisited localities
    return  $unvisitedLocalities;
}

