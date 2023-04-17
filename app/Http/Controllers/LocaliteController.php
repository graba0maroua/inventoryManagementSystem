<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Localite;
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
        if ($user->role_id == 2) { // If user is a center head
            $visitedLocalities = Localite::whereHas('biensScannes', function ($query) use ($user) {
                    $query->where('COP_ID', $user->structure_id);
                })
                ->select('LOC_ID', 'LOC_LIB')
                ->groupBy('LOC_ID', 'LOC_LIB')
                ->get();
        } elseif ($user->role_id == 3) { // If user is a scanning team head
            $groupId = Equipe::where('EMP_ID', $user->matricule)
                ->where('EMP_IS_MANAGER', 1)
                ->value('GROUP_ID');

            $visitedLocalities = Localite::whereHas('biensScannes', function ($query) use ($groupId) {
                    $query->where('GROUP_ID', $groupId);
                })
                ->select('LOC_ID', 'LOC_LIB')
                ->groupBy('LOC_ID', 'LOC_LIB')
                ->get();
        }
        // elseif ($user->role_id == 1) { // If user is unit head
        //     $visitedLocalities = Localite::whereHas('biensScannes', function ($query) use ($user) {
        //             $query->where('EMP_ID', $user->matricule);
        //         })
        //         ->select('LOC_ID', 'LOC_LIB')
        //         ->groupBy('LOC_ID', 'LOC_LIB')
        //         ->get();
        // }

        return response()->json($visitedLocalities);
    }
}
