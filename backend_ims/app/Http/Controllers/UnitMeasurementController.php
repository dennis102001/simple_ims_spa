<?php

namespace App\Http\Controllers;

use App\Models\UnitMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitMeasurementController extends Controller
{

    public function getUnits()
    {
        $units = UnitMeasurement::all()->map(function ($unit) {
            return [
                'id' => $unit->id,
                'unitName' => $unit->unit_name,
                'description' => $unit->description
            ];
        });

        return response()->json([
            'success' => false,
            'unitsListData' => $units
        ], 200);
    }

    public function store(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'unitName' => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:255', 'nullable'],
        ]);

        if ($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 400);
        }

        UnitMeasurement::create([
            'unit_name' => $request->unitName,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Unit measurement added successfully',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validations = Validator::make($request->all(), [
            'unitName' => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:255', 'nullable'],
        ]);

        if ($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 400);
        }

        $unitMeasurement = UnitMeasurement::findOrFail($id);

        $unitMeasurement->update([
            'unit_name' => $request->unitName,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Unit measurement updated successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $unitMeasurement = UnitMeasurement::findOrFail($id);

        $unitMeasurement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit measurement deleted successfully'
        ], 200);
    }
}
