<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Supplier;
use App\Models\UnitMeasurement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function getItems()
    {
        $itemsList = Item::all()->map(function ($item){
            $category = Category::withTrashed()->where('id', $item->category_id)->get()->pluck('category_name')->first();
            $unit = UnitMeasurement::withTrashed()->where('id', $item->unit_id)->get()->pluck('unit_name')->first();

            return [
                'id' => $item->id,
                'itemName' => $item->item_name,
                'description' => $item->description,
                'price' => $item->price,
                'sku' => $item->sku,
                'cost' => $item->cost,
                'quantity' => $item->quantity,
                'categoryId' =>$item->category_id,
                'category' => $category ?? null,
                'unitId' => $item->unit_id,
                'unit' => $unit ?? null,
                'reorderLevel' => $item->reorder_level,
            ];
        });

        $categoriesList = Category::all()->map(function ($category){
            return [
                'id' => $category->id,
                'name' => $category->category_name,
                'description' => $category->description
            ];
        });

        $unitsList = UnitMeasurement::all()->map(function ($unit){
            return [
                'id' => $unit->id,
                'name' => $unit->unit_name,
                'description' => $unit->description
            ];
        });

        $suppliers = Supplier::all()->map(function ($supplier){
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ];
        });

        $customers = Customer::all()->map(function ($customer){
            return [
                'id' => $customer->id,
                'name' => $customer->name,
            ];
        });

        $colors = [
            'lowStockColor' => Color::where('isSelectedForLow', true)->first(),
            'noStockColor' => Color::where('isSelectedForNo', true)->first(),
        ];

        return response()->json([
            'itemsListData' => $itemsList,
            'categoriesListData' => $categoriesList,
            'unitsListData' => $unitsList,
            'suppliersListData' => $suppliers,
            'customersListData' => $customers,
            'colors' => $colors
        ], 200);
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'itemName' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'categoryId' => ['nullable', 'integer'],
            'unitId' => ['nullable', 'integer'],
            'reorderLevel' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validation->fails()){
            return response()->json([
                'success' => false,
                'message' => "Validation failed",
                'errors' => $validation->errors(),
            ], 422);
        }

        Item::create([
            'item_name' => $request->itemName,
            'price' => $request->price,
            'cost' => $request->cost,
            'quantity' => 0,
            'category_id' => $request->categoryId,
            'unit_id' => $request->unitId,
            'reorder_level' => $request->reorderLevel,
            'description' => $request->description,
            'sku' => $request->sku
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item successfully saved'
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $validation = Validator::make($request->all(), [
            'itemName' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'categoryId' => ['nullable', 'integer'],
            'unitId' => ['nullable', 'integer'],
            'reorderLevel' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validation->fails()){
            return response()->json([
                'success' => false,
                'message' => "Validation failed",
                'errors' => $validation->errors(),
            ], 422);
        }

        $item = Item::findOrFail($id);

        $item->update([
            'item_name' => $request->itemName,
            'cost' => $request->cost,
            'price' => $request->price,
            'category_id' => $request->categoryId,
            'unit_id' => $request->unitId,
            'reorder_level' => $request->reorderLevel,
            'description' => $request->description,
            'sku' => $request->sku
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item successfully updated'
        ], 200);
    }

    public function destroy(int $id)
    {
        $item = Item::findOrFail($id);

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item successfully deleted'
        ], 200);
    }
}
