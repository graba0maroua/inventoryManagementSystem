<?php

namespace App\Http\Controllers;

use App\Models\Centre;
use Illuminate\Http\Request;
use App\Models\Localite;
use DB;
class CentreController extends Controller
{

    public function GetAllLOC_by_COP()
    {
        return Localite::with("centre")->get();
        //get all localities in centers
    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
