<?php

namespace App\Http\Controllers;

use App\Models\BiensScannes;
use App\Models\Equipe;
use App\Models\Localite;
use App\Models\Unite;
use Illuminate\Http\Request;
use Auth;

class LocaliteController extends Controller
{
    public function localitesVisites( )
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->role_id == 2) { // If user is chef centre
            $visitedLocalities = Localite::whereHas('biensScannes', function ($query) use ($user) {
                    $query->where('COP_ID', $user->structure_id);
                })
                ->select('LOC_ID', 'LOC_LIB')
                ->groupBy('LOC_ID', 'LOC_LIB')
                ->get();

            // }elseif ($user->role_id == 3) { // If user is a scanning team head
            //     $centerId = $user->structure_id;
            //     $groupId = Equipe::where('EMP_ID', $user->matricule)
            //         ->where('EMP_IS_MANAGER', 1)
            //         ->whereHas('localites', function ($query) use ($centerId) {
            //             $query->where('COP_ID', $centerId);
            //         })
            //         ->value('GROUPE_ID');
            //     $visitedLocalities = Localite::whereHas('biensScannes', function ($query) use ($groupId) {
            //             $query->where('GROUPE_ID', $groupId);
            //         })
            //         ->select('LOC_ID', 'LOC_LIB')
            //         ->groupBy('LOC_ID', 'LOC_LIB')
            //         ->get();
            }elseif ($user->role_id == 3) { // If user is a scanning team head//? ALLAL VERSION
                $centerId = $user->structure_id;
                $visitedLocalities = Localite::whereHas('biensScannes', function ($query) use ($centerId) {
                        $query->whereHas('equipe', function ($query) use ($centerId) {
                            $query->where('COP_ID', $centerId);
                        });
                    })
                    ->select('LOC_ID', 'LOC_LIB')
                    ->groupBy('LOC_ID', 'LOC_LIB')
                    ->get();
            }
          
           elseif ($user->role_id == 1) { // If user is a chef d'unité
                                $unit = Unite::with('Centres.Localites.biensScannes')->find($user->structure_id);
                                $centerIds = $unit->Centres->pluck('COP_ID')->toArray();
                                $visitedLocalities = Localite::whereHas('biensScannes', function ($query) use ($centerIds) {
                                        $query->whereIn('COP_ID', $centerIds);
                                    })
                                    ->select('LOC_ID', 'LOC_LIB')
                                    ->groupBy('LOC_ID', 'LOC_LIB')
                                    ->get();}
    return response()->json($visitedLocalities);
}}
