<?php

namespace App\Http\Controllers;

use App\Models\Unite;
use Illuminate\Http\Request;

class UniteController extends Controller
{

    public function index()
    {
        return Unite::all();
    }

    public function GetLOC_by_UCM(string $id)
    {
        return Unite::find($id)->CentreLocalite;
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
