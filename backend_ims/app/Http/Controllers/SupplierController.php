<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{

    // checked
    public function getSuppliers()
    {
        $suppliers = Supplier::orderBy('id', 'desc')->get()->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'contactPerson' => $supplier->contact_person,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'address' => $supplier->address,
            ];
        });

        return response()->json([
            'success' => true,
            'suppliers' => $suppliers 
        ], 200);
    }

    // checked
    public function store(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'name' => ['string', 'required', 'max:255'],
            'phone' => ['string', 'required', 'max:255'],
            'contactPerson' => ['string', 'nullable', 'max:255'],
            'email' => ['string', 'nullable', 'lowercase', 'email', 'max:255'],
            'address' => ['string', 'nullable', 'max:255'],
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors()
            ], 422);
        }

        Supplier::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'contact_person' => $request->contactPerson,
            'email' => $request->email,
            'address' => $request->address
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully'
        ], 200);
    }

    // checked
    public function update(Request $request, $id)
    {
        $validations = Validator::make($request->all(), [
            'id' => ['integer', 'required', 'exists:suppliers,id'],
            'name' => ['string', 'required', 'max:255'],
            'phone' => ['string', 'required', 'max:255'],
            'contactPerson' => ['string', 'nullable', 'max:255'],
            'email' => ['string', 'nullable', 'lowercase', 'email', 'max:255'],
            'address' => ['string', 'nullable', 'max:255'],
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors()
            ], 422);
        }

        Supplier::where('id', $id)->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'contact_person' => $request->contactPerson,
            'email' => $request->email,
            'address' => $request->address
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully'
        ], 200);
    }

    // checked
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully',
        ], 200);
    }
}
