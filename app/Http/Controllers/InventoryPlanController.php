<?php

namespace App\Http\Controllers;

use App\Models\EquipeLocalite;
use Illuminate\Http\Request;

class InventoryPlanController extends Controller
{

    public function index(Request $request)
    {
        $equipeLocalites = EquipeLocalite::where(
            'GROUPE_ID','like','%'.$request['keyword'].'%')
            ->orWhere('LOC_ID','like','%'.$request['keyword'].'%')
            ->orWhere('COP_ID','like','%'.$request['keyword'].'%')
            ->get();

        return response()->json($equipeLocalites);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'GROUPE_ID' => 'required',
            'LOC_ID' => 'required',
            'COP_ID' => 'required',
        ]);

        // Create a new mapping entry
        $mapping = EquipeLocalite::create($validatedData);

        return response()->json($mapping, 201);
    }


    public function show(string $id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'GROUPE_ID' => 'required',
            'LOC_ID' => 'required',
            'COP_ID' => 'required',
        ]);

        // Find the mapping entry by ID
        $mapping = EquipeLocalite::findOrFail($id);

        // Update the mapping entry with the new data
        $mapping->update($validatedData);

        return response()->json($mapping, 200);
    }

    public function destroy($groupeId, $locId, $copId)
    {
        $deletedRow = equipeLocalite::where('GROUPE_ID', $groupeId)
            ->where('LOC_ID', $locId)
            ->where('COP_ID', $copId)
            ->first();

        if ($deletedRow) {
            $deletedRow->delete();
            return response()->json(['message' => 'Row deleted successfully.']);
        } else {
            return response()->json(['message' => 'Row not found.'], 404);
        }
    }

}
