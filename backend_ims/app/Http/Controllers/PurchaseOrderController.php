<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\SaleDtl;
use App\Models\Supplier;
use App\Models\PoSequence;
use Illuminate\Http\Request;
use App\Models\PurchaseOrderDtl;
use App\Models\PurchaseOrderHdr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{
    // checked
    public function getPOs()
    {
        $hdrs = PurchaseOrderHdr::orderBy('id', 'desc')->get();

        $suppliers = Supplier::all()->map(function ($supplier){
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ];
        });

        $posListData = [];

        foreach($hdrs as $hdr){
            $items = PurchaseOrderDtl::join('items', 'purchase_order_dtls.item_id', '=', 'items.id')->where('hdr_id', $hdr->id)->get()->pluck('item_name')->implode(', ');
            $created_by_name = User::where('id', $hdr->created_by)->pluck('name')->first();
            $received_by_name = User::where('id', $hdr->received_by)->pluck('name')->first();
            $supplier_name = Supplier::where('id', $hdr->supplier_id)->pluck('name')->first();

            $posListData[] = [
                'hdrId' => $hdr->id,
                'poNumber' => $hdr->po_number,
                'orderDate' => $hdr->created_at,
                'createdById' => $hdr->created_by,
                'createdByName' => $created_by_name,
                'receivedById' => $hdr->received_by,
                'receivedByName' => $received_by_name,
                'receivedDate' => $hdr->received_date,
                'remarks' => $hdr->remarks,
                'status' => $hdr->status,
                'supplierId' => $hdr->supplier_id,
                'supplierName' => $supplier_name,
                'items' => $items,
            ];
        }

        return response()->json([
            'success' => true,
            'posListData' => $posListData,
            'suppliersListData' => $suppliers
        ], 200);
    }

    // checked
    public function getPODtlsPending(Request $request)
    {
        $PODtls = PurchaseOrderDtl::where('purchase_order_dtls.hdr_id', $request->query('hdrId'))->get()->map(function($dtl){
            $item = Item::withTrashed()
                ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                ->leftJoin('unit_measurements', 'items.unit_id', '=', 'unit_measurements.id')
                ->where('items.id', $dtl->item_id)
                ->first();

            return [
                'dtlId' => $dtl->id,
                'itemId' => $dtl->item_id,
                'cost' => $dtl->cost,
                'orderedQty' => $dtl->ordered_qty,
                'itemName' => $item->item_name,
                'unit' => $item->unit_name,
                'category' => $item->category_name,
                'sku' => $item->sku,
            ];
        });

        return response()->json([
            'po_details' => $PODtls
        ], 200);
    }

    // checked
    public function getPODtlsReceived(Request $request)
    {
        $PODtls = PurchaseOrderDtl::where('purchase_order_dtls.hdr_id', $request->query('hdrId'))->get()->map(function($dtl){
            $item = Item::withTrashed()
                ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                ->leftJoin('unit_measurements', 'items.unit_id', '=', 'unit_measurements.id')
                ->where('items.id', $dtl->item_id)
                ->first();

            return [
                'dtlId' => $dtl->id,
                'itemId' => $dtl->item_id,
                'cost' => $dtl->cost,
                'orderedQty' => $dtl->ordered_qty,
                'receivedQty' => $dtl->received_qty,
                'itemName' => $item->item_name,
                'unit' => $item->unit_name,
                'category' => $item->category_name,
                'sku' => $item->sku,
            ];
        });

        return response()->json([
            'po_details' => $PODtls
        ], 200);
    }

    // checked
    public function store(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'userId' => ['integer', 'required', 'exists:users,id'],
            'remarks' => ['string', 'nullable', 'max:255'],
            'supplierId' => ['integer', 'nullable', 'exists:suppliers,id'],

            'selectedItemsList' => ['array', 'required'],
            'selectedItemsList.*.id' => ['integer', 'required', 'exists:items,id'],
            'selectedItemsList.*.cost' => ['numeric', 'required'],
            'selectedItemsList.*.orderedQty' => ['integer', 'required', 'min:1']
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 422);
        }

        // FOR PO NUMBER
        $year = now()->year;

        $sequence = PoSequence::firstOrCreate(
            ['year' => $year],
            ['last_number' => 0]
        );

        $sequence->increment('last_number');
        $poNumber = 'PO-' . $year . '-' . str_pad($sequence->last_number, 4, '0', STR_PAD_LEFT);

        $hdr = PurchaseOrderHdr::create([
            'po_number' => $poNumber,
            'created_by' => $request->userId,
            'supplier_id' => $request->supplierId,
            'remarks' => $request->remarks,
            'status' => 'Pending'
        ]);

        foreach($request->selectedItemsList as $item){
            PurchaseOrderDtl::create([
                'hdr_id' => $hdr->id,
                'item_id' => $item['id'],
                'cost' => $item['cost'],
                'ordered_qty' => $item['orderedQty'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase order created successfully',
            'poNumber' => $poNumber
        ], 200);
        
    }

    // checked
    public function receivePO(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'userId' => ['integer', 'required', 'exists:users,id'],
            'remarks' => ['string', 'nullable', 'max:255'],
            'hdrId' => ['integer', 'required', 'exists:purchase_order_hdrs,id'],

            'selectedItemsList' => ['array', 'required'],
            'selectedItemsList.*.itemId' => ['integer', 'required', 'exists:items,id'],
            'selectedItemsList.*.receivedQty' => ['integer', 'required', 'min:1'],
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 422);
        }

        foreach($request->selectedItemsList as $dtl){
            $orderedQty = PurchaseOrderDtl::where('id', $dtl['dtlId'])->pluck('ordered_qty')->first();
            
            if ($orderedQty < $dtl['receivedQty']){
                return response()->json([
                    'success' => false,
                    'message' => 'Received quantity cannot exceed ordered quantity.'
                ], 422);
            }
        }

        PurchaseOrderHdr::where('id', $request->hdrId)->update([
            'received_by' => $request->userId,
            'remarks' => $request->remarks,
            'received_date' => now(),
            'status' => 'Received'
        ]);

        foreach($request->selectedItemsList as $dtl){
            Item::withTrashed()->where('id', $dtl['itemId'])->increment('quantity', $dtl['receivedQty']);

            PurchaseOrderDtl::where('id', $dtl['dtlId'])->update([
                'received_qty' => $dtl['receivedQty'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Items have been successfully received',
        ], 200);
    }

    // checked
    public function updateReceivedPO (Request $request) 
    {
        //validations
        $validations = Validator::make($request->all(), [
            'remarks' => ['string', 'nullable', 'max:255'],
            'hdrId' => ['integer', 'required', 'exists:purchase_order_hdrs,id'],

            'selectedItemsList' => ['array', 'required'],
            'selectedItemsList.*.itemId' => ['integer', 'required', 'exists:items,id' ],
            'selectedItemsList.*.receivedQty' => ['integer', 'required', 'min:1'],
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed here',
                'errors' => $validations->errors(),
            ], 422);
        }

        // validate quantities 
        foreach($request->selectedItemsList as $dtl){
            $orderedQty = PurchaseOrderDtl::where('id', $dtl['dtlId'])->value('ordered_qty');
            $oldQty = PurchaseOrderDtl::where('id', $dtl['dtlId'])->value('received_qty');
            $newQty = $dtl['receivedQty'];
            $currentQty = Item::withTrashed()->where('id', $dtl['itemId'])->value('quantity');

            // received quantity cannot exceed ordered quantity
            if ($orderedQty < $newQty){
                return response()->json([
                    'success' => false,
                    'message' => 'Received quantity cannot exceed ordered quantity.'
                ], 422);
            }

            // old quantity greater than new quantity should deduct the difference to the current quantity of the item
            if($oldQty > $newQty){
                $diff = $oldQty - $newQty;

                if($diff > $currentQty){
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot update: one or more items have insufficient stock to roll back'
                    ], 422);
                }else{
                    // deduct the diff to the item's quantity
                }
            }
        }

        PurchaseOrderHdr::where('id', $request->hdrId)->update([
            'received_by' => $request->userId,
            'remarks' => $request->remarks,
            'received_date' => now(),
            'status' => 'Received'
        ]);

        foreach($request->selectedItemsList as $dtl){
            $oldQty = PurchaseOrderDtl::where('id', $dtl['dtlId'])->value('received_qty');
            $newQty = $dtl['receivedQty'];

            if($newQty > $oldQty){
                $diff = $newQty - $oldQty;
                Item::withTrashed()->where('id', $dtl['itemId'])->increment('quantity', $diff);
            }else{
                $diff = $oldQty - $newQty;
                Item::withTrashed()->where('id', $dtl['itemId'])->decrement('quantity', $diff);
            }

            PurchaseOrderDtl::where('id', $dtl['dtlId'])->update([
                'received_qty' => $dtl['receivedQty'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase order updated successfully',
        ], 200);
    }

    // checked
    public function updatePendingPO(Request $request)
    {
        //validations
        $validations = Validator::make($request->all(), [
            'hdrId' => ['integer', 'required', 'exists:purchase_order_hdrs,id'],
            'remarks' => ['string', 'nullable', 'max:255'],
            'supplierId' => ['integer', 'nullable', 'exists:suppliers,id'],

            'selectedItemsList' => ['array', 'required'],
            'selectedItemsList.*.itemId' => ['integer', 'required', 'exists:items,id'],
            'selectedItemsList.*.orderedQty' => ['integer', 'required', 'min:1'],
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 422);
        }

        //SAVE UPDATED PURCHASE ORDER HEADER
        PurchaseOrderHdr::where('id', $request->hdrId)->update([
            'remarks' => $request->remarks,
            'supplier_id' => $request->supplierId,
        ]);

        //FOR REMOVED ITEMS
        foreach($request->removedDtlIds as $dtlId){
            PurchaseOrderDtl::destroy($dtlId);
        }

        //SAVE UPDATED AND ADDED ITEMS
        foreach($request->selectedItemsList as $dtl){
            if($dtl['dtlId']){
                PurchaseOrderDtl::where('id', $dtl['dtlId'])->update([
                    'ordered_qty' => $dtl['orderedQty'],
                ]);
            }
            else{
                PurchaseOrderDtl::create([
                    'hdr_id' => $request->hdrId,
                    'item_id' => $dtl['itemId'],
                    'cost' => $dtl['cost'],
                    'ordered_qty' => $dtl['orderedQty'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase order updated successfully',
        ], 200);
    }

    // checked
    public function cancelPO(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'hdrId' => ['integer', 'required', 'exists:purchase_order_hdrs,id'],
            'remarks' => ['string', 'nullable', 'max:255'],
            'status' => ['string', 'required', 'max:255']
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 422);
        }

        if($request->status !== 'Pending'){
            return response()->json([
                'success' => false,
                'message' => 'You can only cancel a pending purchase order'
            ], 422);
        }

        $hdr = PurchaseOrderHdr::findOrFail($request->hdrId);

        $hdr->update([
            'remarks' => $request->remarks,
            'status' => 'Cancelled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Purchase order cancelled successfully'
        ], 200);

    }

    public function returnPO(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'hdrId' => ['integer', 'required', 'exists:purchase_order_hdrs,id'],
            'remarks' => ['string', 'nullable', 'max:255'],
            'status' => ['string', 'required', 'max:255']
        ]);

        if($validations->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validations->errors(),
            ], 422);
        }

        if($request->status !== 'Received'){
            return response()->json([
                'success' => false,
                'message' => 'You can only return a purchase order after you received it'
            ], 422);
        }

        foreach($request->selectedItemsList as $dtl){
            $currentQty = Item::withTrashed()->where('id', $dtl['itemId'])->value('quantity');
            $receivedQty = $dtl['receivedQty'];

            if($currentQty < $receivedQty){
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot return: one or more items have insufficient stock to roll back'
                ], 422);
            }
        }

        $hdr = PurchaseOrderHdr::findOrFail($request->hdrId);

        $hdr->update([
            'remarks' => $request->remarks,
            'status' => 'Returned'
        ]);

        foreach($request->selectedItemsList as $dtl){
            Item::withTrashed()->where('id', $dtl['itemId'])->decrement('quantity', $dtl['receivedQty']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Purchase order returned successfully'
        ], 200);
    }


}
