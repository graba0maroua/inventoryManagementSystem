<?php

namespace App\Http\Controllers;

use App\Models\Unite;
use Illuminate\Http\Request;

class UniteController extends Controller
{

    public function index()
    {
        return Unite::all();   //get all unites
    }

    public function GetLOC_by_UCM(string $id)
    {
        return Unite::find($id)->CentreLocalite; //get localite by unite id
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
