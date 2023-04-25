<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use Illuminate\Http\Request;

class EquipeController extends Controller
{
    public function store(Request $request)
    {
         $request->validate([
'GROUPE_ID'=> 'required',
'YEAR'=> 'required',
'COP_ID'=> 'required',
'EMP_ID'=> 'required',
'EMP_FULLNAME'=> 'required',
'EMP_IS_MANAGER'=> 'required'

         ],); //costom error message foe eachfield
        return Equipe::create($request->all());
    }
}
