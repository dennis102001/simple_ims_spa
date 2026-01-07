<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\StockTransactionDtl;
use App\Models\StockTransactionHdr;
use Illuminate\Support\Facades\Validator;

class StockTransactionController extends Controller
{
    public function storeStockIn(Request $request)
    {
        //validations
        $validation = Validator::make($request->all(), [
            'userId' => ['integer', 'required', 'exists:users,id'],
            'userName' => ['string', 'required', 'max:255'],
            'remarks' => ['string', 'required', 'max:255'],

            'selectedItemsList' => ['array', 'required'],
            'selectedItemsList.*.itemId' => ['integer', 'required', 'exists:items,id'],
            'selectedItemsList.*.itemName' => ['string', 'required'],
            'selectedItemsList.*.itemCost' => ['numeric', 'required'],
            'selectedItemsList.*.itemPrice' => ['numeric', 'required'],
            'selectedItemsList.*.quantityToAdd' => ['integer', 'required', 'min:1'],
        ]);

        if ($validation->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors()
            ], 422);
        }

        $hdr = StockTransactionHdr::create([
            'user_id' => $request->userId,
            'user_name' => $request->userName,
            'remarks' => $request->remarks,
            'type' => 'Stock In',
        ]);

        foreach($request->selectedItemsList as $item){
            $currentQty = Item::where('id', $item['itemId'])->value('quantity');

            StockTransactionDtl::create([
                'item_id' => $item['itemId'],
                'item_name' => $item['itemName'],
                'cost' => $item['itemCost'],
                'price' => $item['itemPrice'],
                'quantity' => $item['quantityToAdd'],
                'hdr_id' => $hdr->id,
            ]);

            Item::where('id', $item['itemId'])->update([
                'quantity' => $currentQty + $item['quantityToAdd']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully saved'
        ], 200);
    }

    public function storeStockOut(Request $request)
    {
        // validations
        $validation = Validator::make($request->all(), [
            'userId' => ['integer', 'required', 'exists:users,id'],
            'userName' => ['string', 'required', 'max:255'],
            'remarks' => ['string', 'required', 'max:255'],

            'selectedItemsList' => ['array', 'required'],
            'selectedItemsList.*.itemId' => ['integer', 'required', 'exists:items,id'],
            'selectedItemsList.*.itemName' => ['string', 'required'],
            'selectedItemsList.*.itemCost' => ['numeric', 'required'],
            'selectedItemsList.*.itemPrice' => ['numeric', 'required'],
            'selectedItemsList.*.quantityToOut' => ['integer', 'required', 'min:1'],
        ]);

        if ($validation->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors()
            ], 422);
        }

        //validation / if items' qty is less than qty in stock out, then return
        foreach($request->selectedItemsList as $selectedItem){
            $item = Item::find($selectedItem['itemId']);
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found.',
                ], 400);
            }

            if ($item->quantity < $selectedItem['quantityToOut']){
                return response()->json([
                    'success' => false,
                    'message' => 'Some items do not have enough stocks.'
                ], 400);
            }    
        }

        $hdr = StockTransactionHdr::create([
            'user_id' => $request->userId,
            'user_name' => $request->userName,
            'remarks' => $request->remarks,
            'type' => 'Stock Out',
        ]);

        foreach($request->selectedItemsList as $item){
            $currentQty = Item::where('id', $item['itemId'])->value('quantity');

            StockTransactionDtl::create([
                'item_id' => $item['itemId'],
                'item_name' => $item['itemName'],
                'cost' => $item['itemCost'],
                'price' => $item['itemPrice'],
                'quantity' => $item['quantityToOut'],
                'hdr_id' => $hdr->id,
            ]);

            Item::where('id', $item['itemId'])->update([
                'quantity' => $currentQty - $item['quantityToOut']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully saved'
        ], 200);
    }

    Public function getStockReport() 
    {
        $hdrs = StockTransactionHdr::orderBy('id', 'desc')->get();
        $result = [];

        foreach($hdrs as $hdr){
            $items = StockTransactionDtl::where('hdr_id', $hdr->id)->get()->pluck('item_name')->implode(', ');

            $result[] = [
                'id' => $hdr->id,
                'date' => $hdr->created_at,
                'user_name' => $hdr->user_name,
                'remarks' => $hdr->remarks,
                'items' => $items,
                'type' => $hdr->type
            ];
        }
        
        return $result;
    }

    public function getStockReportSpecified(Request $request)
    {
        $dateFrom = $request->query('from') . ' 00:00:00';
        $dateTo = $request->query('to') . ' 23:59:59';

        $hdrs = StockTransactionHdr::whereBetween('created_at', [$dateFrom, $dateTo])->orderBy('id', 'desc')->get();
        $result = [];

        foreach($hdrs as $hdr){
            $items = StockTransactionDtl::where('hdr_id', $hdr->id)->get()->pluck('item_name')->implode(', ');

            $result[] = [
                'id' => $hdr->id,
                'date' => $hdr->created_at,
                'user_name' => $hdr->user_name,
                'remarks' => $hdr->remarks,
                'items' => $items
            ];
        }
        
        return $result;
    }

    public function getStockTransactionDetails(Request $request)
    {
        $hdr = StockTransactionHdr::findOrFail($request->query('id'));

        return StockTransactionDtl::where('hdr_id', $hdr->id)->get();
    }

    public function update(Request $request, string $id)
    {
        // validations
        $validation = Validator::make($request->all(), [
            'userId' => ['integer', 'required', 'exists:users,id'],
            'userName' => ['string', 'required', 'max:255'],
            'remarks' => ['string', 'required', 'max:255'],

            'selectedItemsList' => ['array', 'required'],
            'selectedItemsList.*.item_id' => ['integer', 'required', 'exists:items,id'],
            'selectedItemsList.*.item_name' => ['string', 'required'],
            'selectedItemsList.*.cost' => ['numeric', 'required'],
            'selectedItemsList.*.price' => ['numeric', 'required'],
            'selectedItemsList.*.quantity' => ['integer', 'required', 'min:1'],
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->errors()
            ], 422);
        }

        $hdr = StockTransactionHdr::find($id);

        if($hdr->type == 'Stock In'){

            //when removing some stock-in qty / the removed qty > available stock qty
            foreach($request->selectedItemsList as $newDtl){
                $oldDtl = StockTransactionDtl::find($newDtl['id']);

                if ($oldDtl->quantity > $newDtl['quantity']){
                    $diff = $oldDtl->quantity - $newDtl['quantity'];
                    $item = Item::find($oldDtl->item_id);

                    if($item->quantity < $diff){
                        return response()->json([
                            'success' => false,
                            'message' => 'Some item\'s stock to be removed is greater than current stock.'
                        ], 400);
                    }
                }
            }

            //when deleting items stock-in qty > available stock qty
            foreach($request->deletedItemsList as $deletedDtlId){
                $dtl = StockTransactionDtl::find($deletedDtlId);
                $item = Item::find($dtl->item_id);

                if ($dtl->quantity > $item->quantity){
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete because quantity is greater than available stock.'
                    ], 400);
                }
                else{
                    $item->decrement('quantity', $dtl->quantity);
                    StockTransactionDtl::destroy($dtl->id);
                }
            }

            $hdr->update([
                'user_id' => $request->userId,
                'user_name' => $request->userName,
                'remarks' => $request->remarks,
            ]);

            foreach($request->selectedItemsList as $newDtl){
                $oldDtl = StockTransactionDtl::find($newDtl['id']);

                if ($oldDtl->quantity >= $newDtl['quantity']){
                    $item = Item::find($oldDtl->item_id);

                    $item->decrement('quantity', $oldDtl->quantity - $newDtl['quantity']);

                    StockTransactionDtl::where('id', $newDtl['id'])->update(['quantity' => $newDtl['quantity']]);
                }
                else{
                    $item = Item::find($oldDtl->item_id);

                    $item->increment('quantity', $newDtl['quantity'] - $oldDtl->quantity);

                    StockTransactionDtl::where('id', $newDtl['id'])->update(['quantity' => $newDtl['quantity']]);
                }   
            }
        }
        else
        {
            // when adding qty to stock out items / added qty to stock out > available stocks qty
            foreach($request->selectedItemsList as $newDtl){
                $oldDtl = StockTransactionDtl::find($newDtl['id']);
                $item = Item::find($oldDtl->item_id);
                
                if ($oldDtl->quantity < $newDtl['quantity']){
                    $diff = $newDtl['quantity'] - $oldDtl->quantity;

                    if($item->quantity < $diff){
                        return response()->json([
                            'success' => false,
                            'message' => 'Some items do not have enough stocks.'
                        ], 400);
                    }
                }    
            }

            //deleted items from stock-out also means returning the quantity removed from items 
            foreach($request->deletedItemsList as $deletedDtlId){
                $dtl = StockTransactionDtl::find($deletedDtlId);
                $item = Item::find($dtl->item_id);

                $item->increment('quantity', $dtl->quantity);
                StockTransactionDtl::destroy($dtl->id);
            }

            $hdr->update([
                'user_id' => $request->userId,
                'user_name' => $request->userName,
                'remarks' => $request->remarks,
            ]);

            foreach($request->selectedItemsList as $newDtl){
                $oldDtl = StockTransactionDtl::find($newDtl['id']);

                if ($oldDtl->quantity >= $newDtl['quantity']){
                    $item = Item::find($oldDtl->item_id);

                    $item->increment('quantity', $oldDtl->quantity - $newDtl['quantity']);

                    StockTransactionDtl::where('id', $newDtl['id'])->update(['quantity' => $newDtl['quantity']]);
                }
                else{
                    $item = Item::find($oldDtl->item_id);

                    $item->decrement('quantity', $newDtl['quantity'] - $oldDtl->quantity);

                    StockTransactionDtl::where('id', $newDtl['id'])->update(['quantity' => $newDtl['quantity']]);
                }   
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated'
        ], 200);
    }

    public function delete($id)
    {
        $hdr = StockTransactionHdr::findOrFail($id);
        $dtls = StockTransactionDtl::where('hdr_id', $hdr->id)->get();

        if($hdr->type == 'Stock In'){

            foreach($dtls as $dtl){
                $item = Item::find($dtl->item_id);

                if($dtl->quantity > $item->quantity){
                    return response()->json(['error' => 'Some item\'s stock to be removed is greater than current stock.'], 400);
                }
                else{
                    $item->decrement('quantity', $dtl->quantity);
                    StockTransactionDtl::destroy($dtl->id);
                }
            }
        }
        else{
            foreach($dtls as $dtl){
                $item = Item::find($dtl->item_id);

                $item->increment('quantity', $dtl->quantity);
                StockTransactionDtl::destroy($dtl->id);
            }
        }

        StockTransactionHdr::destroy($hdr->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted'
        ], 200);

    }
}
