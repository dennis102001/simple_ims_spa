<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\SaleDtl;
use App\Models\SaleHdr;
use App\Models\Customer;
use App\Models\SoSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{

    // checked
    public function getSales()
    {
        $hdrs = SaleHdr::orderBy('id', 'desc')->get();

        $salesListData = [];
        $customers = Customer::all()->map(function ($customer){
            return [
                'id' => $customer->id,
                'name' => $customer->name,
            ];
        });

        foreach($hdrs as $hdr){
            $items = SaleDtl::join('items', 'sale_dtls.item_id', '=', 'items.id')->where('hdr_id', $hdr->id)->get()->pluck('item_name')->implode(', ');
            $created_by_name = User::where('id', $hdr->created_by)->pluck('name')->first();
            $confirmed_by_name = User::where('id', $hdr->confirmed_by)->pluck('name')->first();
            $customer_name = Customer::where('id', $hdr->customer_id)->pluck('name')->first();

            $salesListData[] = [
                'hdrId' => $hdr->id,
                'soNumber' => $hdr->so_number,
                'createdDate' => $hdr->created_at,
                'createdById' => $hdr->created_by,
                'createdByName' => $created_by_name,
                'confirmedById' => $hdr->confirmed_by,
                'confirmedByName' => $confirmed_by_name,
                'confirmedDate' => $hdr->confirmed_date,
                'remarks' => $hdr->remarks,
                'items' => $items,
                'status' => $hdr->status,
                'customerId' => $hdr->customer_id,
                'customerName' => $customer_name,
            ];
        }

        return response()->json([
            'success' => true,
            'salesListData' => $salesListData,
            'customersListData' => $customers
        ], 200);
    }

    // checked
    public function store(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'userId' => ['integer', 'required', 'exists:users,id'],
            'remarks' => ['string', 'nullable', 'max:255'],
            'customerId' => ['integer', 'nullable', 'exists:customers,id'],

            'selectedItemsList' => ['array', 'required'],
            'selectedItemsList.*.id' => ['integer', 'required'], 'exists:items,id',
            'selectedItemsList.*.price' => ['numeric', 'required'],
            'selectedItemsList.*.quantity' => ['integer', 'required', 'min:1']
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 422);
        }

        //CHECK ITEMS' QUANTITY
        foreach($request->selectedItemsList as $selectedItem){
            $itemQty = Item::where('id', $selectedItem['id'])->pluck('quantity')->first();

            if($selectedItem['quantity'] > $itemQty){
                return response()->json([
                'success' => false,
                'message' => 'One or more items have insufficient stock',
            ], 400);
            }
        }

        // FOR SALE ORDER NUMBER
        $year = now()->year;

        $sequence = SoSequence::firstOrCreate(
            ['year' => $year],
            ['last_number' => 0]
        );

        $sequence->increment('last_number');
        $soNumber = 'SO-' . $year . '-' . str_pad($sequence->last_number, 4, '0', STR_PAD_LEFT);


        $hdr = SaleHdr::create([
            'so_number' => $soNumber,
            'created_by' => $request->userId,
            'customer_id' => $request->customerId,
            'remarks' => $request->remarks,
            'status' => 'Pending',
        ]);

        foreach($request->selectedItemsList as $selectedItem){
            SaleDtl::create([
                'hdr_id' => $hdr->id,
                'item_id' => $selectedItem['id'],
                'quantity' => $selectedItem['quantity'],
                'price' => $selectedItem['price'],
            ]);

            Item::where('id', $selectedItem['id'])->decrement('quantity', $selectedItem['quantity']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sale created successfully',
            'soNumber' => $soNumber
        ], 200);

    }

    // checked
    public function getSalesDtls(Request $request)
    {
        $SaleDtls = SaleDtl::where('sale_dtls.hdr_id', $request->query('hdrId'))->get()->map(function($dtl){
            $item = Item::withTrashed()
                ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                ->leftJoin('unit_measurements', 'items.unit_id', '=', 'unit_measurements.id')
                ->where('items.id', $dtl->item_id)
                ->first();

            return [
                'dtlId' => $dtl->id,
                'itemId' => $dtl->item_id,
                'price' => $dtl->price,
                'quantity' => $dtl->quantity,
                'itemName' => $item->item_name,
                'unit' => $item->unit_name,
                'sku' => $item->sku,
            ];
        });

        return response()->json([
            'sale_details' => $SaleDtls
        ], 200);
    }

    // checked
    public function update(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'hdrId' => ['required', 'integer', 'exists:sale_hdrs,id'],
            'customerId' => ['nullable', 'integer', 'exists:customers,id'],
            'remarks' => ['nullable', 'string', 'max:255'],

            'selectedItemsList' => ['required', 'array'],
            'selectedItemsList.*.itemId' => ['required', 'integer', 'exists:items,id'],
            'selectedItemsList.*.quantity' => ['required', 'integer', 'min:0'],
        ]);

        if ($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 400);
        }
        
        $hdr = SaleHdr::where('id', $request->hdrId)->first();

        if($hdr->status !== "Pending"){
            return response()->json([
                'success' => false,
                'message' => "Conflict with the sale status"
            ], 400);
        }

        //CHECK ITEMS' QUANTITY
        foreach($request->selectedItemsList as $dtl){

            if($dtl['dtlId']){
                $itemQty = Item::withTrashed()->where('id', $dtl['itemId'])->pluck('quantity')->first();
                $oldQty = SaleDtl::where('id', $dtl['dtlId'])->pluck('quantity')->first();
                $newQty = $dtl['quantity'];
                
                if($oldQty > $newQty){
                    //return diff
                }
                elseif($newQty > $oldQty){
                    $diff = ($newQty - $oldQty);

                    if($itemQty < $diff){
                        return response()->json([
                            'success' => false,
                            'message' => 'One or more items have insufficient stocks'
                        ], 400);
                    }
                    else {
                        //subtract the diff from item's quantity
                    }
                }
            }
            else { 
                $itemQty = Item::where('id', $dtl['itemId'])->pluck('quantity')->first();
                if($dtl['quantity'] > $itemQty){
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more items have insufficient stock'
                    ], 400);
                }
            }
        }

        //FOR REMOVED ITEMS
        foreach($request->removedDtlIds as $dtlId){
            $dtl = SaleDtl::where('id', $dtlId)->first();

            Item::withTrashed()->where('id', $dtl->item_id)->increment('quantity', $dtl->quantity);

            SaleDtl::destroy($dtlId);
        }

        $hdr->update([
            'remarks' => $request->remarks,
            'customer_id' => $request->customerId
        ]);

        foreach($request->selectedItemsList as $dtl){
            if($dtl['dtlId']){
                $itemQty = Item::withTrashed()->where('id', $dtl['itemId'])->pluck('quantity')->first();
                $oldQty = SaleDtl::where('id', $dtl['dtlId'])->pluck('quantity')->first();
                $newQty = $dtl['quantity'];
                
                if($oldQty > $newQty){
                    $diff = ($oldQty - $newQty);
                    Item::withTrashed()->where('id', $dtl['itemId'])->increment('quantity', $diff);
                }
                elseif($newQty > $oldQty){
                    $diff = ($newQty - $oldQty);

                    if($itemQty >= $diff){
                        Item::withTrashed()->where('id', $dtl['itemId'])->decrement('quantity', $diff);
                    }
                }

                SaleDtl::where('id', $dtl['dtlId'])->update([
                    'quantity' => $dtl['quantity'],
                ]);
            }
            else{
                SaleDtl::create([
                    'hdr_id' => $request->hdrId,
                    'item_id' => $dtl['itemId'],
                    'quantity' => $dtl['quantity'],
                    'price' => $dtl['price'],
                ]);

                Item::where('id', $dtl['itemId'])->decrement('quantity', $dtl['quantity']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Sale updated successfully',
        ], 200);
    }

    // checked
    public function cancelSale(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'hdrId' => ['required', 'integer', 'exists:sale_hdrs,id'],
            'remarks' => ['nullable', 'string', 'max:255']
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => "Validation failed"
            ], 400);
        }

        $hdr = SaleHdr::findOrFail($request->hdrId);

        if($hdr->status === "Pending"){
            $hdr->update([
                'status' => 'Cancelled',
                'remarks' => $request->remarks
            ]);

            $dtls = SaleDtl::where('hdr_id', $hdr->id)->get();

            foreach($dtls as $dtl)
            {
                Item::withTrashed()->where('id', $dtl->item_id)->increment('quantity', $dtl->quantity);
            }

            return response()->json([
                'success' => true,
                'message' => "Sale cancelled successfully"
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => "Conflict with the sale status"
            ], 400);
        }
        
    }

    // checked
    public function paySale(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'userId' => ['integer', 'required', 'exists:users,id'],
            'hdrId' => ['required', 'integer', 'exists:sale_hdrs,id'],
            'remarks' => ['nullable', 'string', 'max:255']
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => "Validation failed"
            ], 400);
        }

        $hdr = SaleHdr::findOrFail($request->hdrId);

        if($hdr->status === "Pending"){
            $hdr->update([
                'status' => 'Paid',
                'remarks' => $request->remarks,
                'confirmed_by' => $request->userId,
                'confirmed_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Sale paid successfully"
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => "Conflict with the sale status"
            ], 400);
        }
        
    }

    // checked
    public function returnSale(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'hdrId' => ['required', 'integer', 'exists:sale_hdrs,id'],
            'remarks' => ['nullable', 'string', 'max:255']
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => "Validation failed"
            ], 400);
        }

        $hdr = SaleHdr::findOrFail($request->hdrId);

        if($hdr->status === "Paid"){
            $hdr->update([
                'status' => 'Returned',
                'remarks' => $request->remarks
            ]);

            $dtls = SaleDtl::where('hdr_id', $hdr->id)->get();

            foreach($dtls as $dtl)
            {
                Item::withTrashed()->where('id', $dtl->item_id)->increment('quantity', $dtl->quantity);
            }

            return response()->json([
                'success' => true,
                'message' => "Sale returned successfully"
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => "Conflict with the sale status"
            ], 400);
        }
        
    }

}
