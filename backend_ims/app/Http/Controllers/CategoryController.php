<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function getCategories()
    {
        $categories_list = Category::all()->map(function ($category) {
            return [
                "id" => $category->id,
                "categoryName" => $category->category_name,
                "description" => $category->description
            ];
        });

        return response()->json([
            'success' => true,
            'categories_list' => $categories_list,
        ]);
    }

    public function store(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'categoryName' => ['required', 'string', 'max:255' ],
            'description' => ['string', 'max:255', 'nullable'],
        ]);

        if ($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 400);
        }

        Category::create([
            'category_name' => $request->categoryName,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category added successfully',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validations = Validator::make($request->all(), [
            'categoryName' => ['required', 'string', 'max:255' ],
            'description' => ['string', 'max:255', 'nullable' ],
        ]);

        if ($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 400);
        }

        $category = Category::findOrFail($id);

        $category->update([
            'category_name' => $request->categoryName,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
        ], 200);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ], 200); 
    }
}
