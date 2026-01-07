<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColorController extends Controller
{

    // checked
    public function getColors()
    {
        $colorsListData = Color::all()->map(function($color){
            return [
                'id' => $color->id,
                'colorName' => $color->color_name,
                'isSelectedForLow' => $color->isSelectedForLow,
                'isSelectedForNo' => $color->isSelectedForNo
            ];
        });

        return response()->json([
            'success' => true,
            'colorsListData' => $colorsListData
        ], 200);
    }

    // checked
    public function setColor(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'type' => ['required', 'string', 'max:10'],

            'color' => ['nullable', 'array'],
            'color.id' => ['nullable', 'integer', 'exists:colors,id'],
            'color.colorName' => ['nullable', 'string', 'max:20'],
            'color.isSelectedForLow' => ['nullable', 'integer'],
            'color.isSelectedForNo' => ['nullable', 'integer'],
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors()
            ], 422);
        }

        if($request->type === "Low Stock"){
            if($request->color){
                Color::where('isSelectedForLow', true)->update(['isSelectedForLow' => false]);
                Color::where('id', $request->color['id'])->update(['isSelectedForLow' => true]);
            }else{
                Color::where('isSelectedForLow', true)->update(['isSelectedForLow' => false]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Color for low stock items updated successfully'
            ], 200);
        }
        else {
            if($request->color){
                Color::where('isSelectedForNo', true)->update(['isSelectedForNo' => false]);
                Color::where('id', $request->color['id'])->update(['isSelectedForNo' => true]);
            }else{
                Color::where('isSelectedForNo', true)->update(['isSelectedForNo' => false]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Color for no stock items updated successfully'
            ], 200);
        }

        
    }

}
