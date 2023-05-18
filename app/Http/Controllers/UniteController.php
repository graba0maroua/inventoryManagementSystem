<?php

namespace App\Http\Controllers;

use App\Models\Centre;
use App\Models\Localite;
use App\Models\Unite;
use Illuminate\Http\Request;

class UniteController extends Controller
{

    public function index()
    {
        return Unite::all();
    }

    public function centreAll( )
    {
         return response()->json(["centres"=>Centre::all(),
         "unites"=>Unite::all()],200);
    }
    public function centres( Request $request)
    {
         return response()->json(["centres"=>Centre::where('COP_LIB','like','%'.$request['keyword'].'%')->get()],200); //get localite by unite id
    }
    public function localiteAll()
    {
         return Localite::all();
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
