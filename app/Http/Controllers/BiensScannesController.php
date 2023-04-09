<?php

namespace App\Http\Controllers;

use App\Models\BiensScannes;
use Illuminate\Http\Request;

class BiensScannesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BiensScannes::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return BiensScannes::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
