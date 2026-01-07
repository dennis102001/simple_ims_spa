<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // checked
    public function getCustomers()
    {
        $customers = Customer::orderBy('id', 'desc')->get()->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $customer->address,
            ];
        });

        return response()->json([
            'success' => true,
            'customers' => $customers 
        ], 200);
    }

    // checked
    public function store(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'name' => ['string', 'required', 'max:255'],
            'phone' => ['string', 'required', 'max:255'],
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

        Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully'
        ], 200);
    }

    // checked
    public function update(Request $request, $id)
    {
        $validations = Validator::make($request->all(), [
            'id' => ['integer', 'required', 'exists:customers,id'],
            'name' => ['string', 'required', 'max:255'],
            'phone' => ['string', 'required', 'max:255'],
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

        Customer::where('id', $id)->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully'
        ], 200);
    }

    // checked
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ], 200);
    }
}
