<?php

namespace App\Http\Controllers;

use App\Models\Assets;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
    public function fill_LOC_ID_INIT(){
    $nullAssets = Assets::whereNull('LOC_ID_INIT')->get();

$randomLocalities = ['0900L00000002', '0900L00000007', '0900L00000008'];

 foreach ($nullAssets as $asset) {
    $randomIndex = array_rand($randomLocalities); //we get a random index/position
    $randomLocality = $randomLocalities[$randomIndex]; //get the LOC_ID of that index

    $asset->LOC_ID_INIT = $randomLocality; //fill the null value with it
    $asset->save();
}
    }
}
