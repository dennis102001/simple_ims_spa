<?php

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\SaleHdr;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\PurchaseOrderDtl;
use App\Models\PurchaseOrderHdr;
use Illuminate\Support\Facades\DB;
use App\Models\StockTransactionDtl;
use App\Models\StockTransactionHdr;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\UnitMeasurementController;
use App\Http\Controllers\StockTransactionController;
use Carbon\CarbonPeriod;

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/home-info', function(Request $request) {
        $totalItems = Item::count();
        $totalCategories = Category::count();
        $zeroStock = Item::where('quantity', '<=', 0)->count();
        $lowStock = Item::whereColumn('quantity', '<=', 'reorder_level')->count();

        $recentOrders = PurchaseOrderHdr::orderByDesc('id')->limit(5)->get()->map(function ($po){
            return [
                'orderDate' => $po->created_at,
                'poNumber' => $po->po_number,
                'status' => $po->status
            ];
        });

        $recentSales = SaleHdr::orderByDesc('id')->limit(5)->get()->map(function($sale){
            return [
                'soNumber' => $sale->so_number,
                'createdDate' => $sale->created_at,
                'status' => $sale->status
            ];
        });

        $topItems = Item::select('items.id', 'items.item_name', DB::raw('SUM(sale_dtls.quantity * sale_dtls.price) as total_sold'))
            ->join('sale_dtls', 'items.id', '=', 'sale_dtls.item_id')
            ->groupBy('items.id', 'items.item_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $now = Carbon::now()->endOfDay();
        $start = Carbon::now()->subDays(7)->startOfDay();

        $dates = [];
        foreach(CarbonPeriod::create($start, $now) as $date){
            $dates[] = $date->format('Y-m-d');
        }

        $weeklyQtyIn = DB::table('purchase_order_hdrs as hdr')
            ->join('purchase_order_dtls as dtl', 'hdr.id', '=', 'dtl.hdr_id')
            ->selectRaw('DATE(hdr.created_at) as date, SUM(dtl.received_qty) as total_in')
            ->where('hdr.status', 'Received')
            ->whereBetween('hdr.created_at', [$start, $now])
            ->groupBy('date')
            ->orderBy('date')
            ->limit(7)
            ->get()
            ->pluck('total_in', 'date')
            ->toArray();

        $weeklyQtyOut = DB::table('sale_hdrs as hdr')
            ->join('sale_dtls as dtl', 'hdr.id', '=', 'dtl.hdr_id')
            ->selectRaw('DATE(hdr.created_at) as date, SUM(dtl.quantity) as total_out')
            ->whereIn('hdr.status', ['Completed', 'Paid'])
            ->whereBetween('hdr.created_at', [$start, $now])
            ->groupBy('date')
            ->orderBy('date')
            ->limit(7)
            ->get()
            ->pluck('total_out', 'date')
            ->toArray();

        $stocksMovementData = [];
        foreach($dates as $date){
            $stocksMovementData[] = [
                'date' => $date,
                'total_in' => $weeklyQtyIn[$date] ?? 0,
                'total_out' => $weeklyQtyOut[$date] ?? 0,
            ];
        }

        $revenueThisMonth = SaleHdr::join('sale_dtls', 'sale_hdrs.id', '=', 'sale_dtls.hdr_id')
            ->selectRaw('SUM(sale_dtls.quantity * sale_dtls.price) AS total_revenue')
            ->whereBetween('sale_hdrs.created_at', [$request->start, $request->end])
            ->whereNotIn('sale_hdrs.status', ['Cancelled', 'Returned'])
            ->first();

        $revenue = [
            'start' => $request->start, 
            'end' => $request->end, 
            'totalRevenue' => $revenueThisMonth->total_revenue
        ];

        return response()->json([
            'totalItems' => $totalItems,
            'totalCategories' => $totalCategories,
            'zeroStock' => $zeroStock,
            'lowStock' => $lowStock,
            'recentOrders' => $recentOrders,
            'recentSales' => $recentSales,
            'topSellingData' => $topItems,
            'stocksMovementData' => $stocksMovementData,
            'revenue' => $revenue
        ]);

    });

    Route::controller(ItemController::class)->group(function(){
        Route::get('/items-list', 'getItems');
        Route::post('/store-item', 'store');
        Route::put('/update-item/{id}', 'update');
        Route::delete('/delete-item/{id}', 'destroy');
    });

    Route::controller(StockTransactionController::class)->group(function(){
        Route::post('/stock-in', 'storeStockIn');
        Route::post('/stock-out', 'storeStockOut');
        Route::get('/stock-report', 'getStockReport');
        Route::get('/stock-report-specified', 'getStockReportSpecified');
        Route::get('/stock-transaction-details', 'getStockTransactionDetails');
        Route::put('/update-transaction/{id}', 'update');
        Route::delete('/delete-transaction/{id}', 'delete');
    });

    Route::controller(CategoryController::class)->group(function(){
        Route::get('/categories-list', 'getCategories');
        Route::post('/add-category', 'store');
        Route::put('/update-category/{id}', 'update');
        Route::delete('/delete-category/{id}', 'destroy');
    });

    Route::controller(UnitMeasurementController::class)->group(function(){
        Route::get('/units-list', 'getUnits');
        Route::post('/add-unit', 'store');
        Route::put('/update-unit/{id}', 'update');
        Route::delete('/delete-unit/{id}', 'destroy');
    });

    Route::controller(PurchaseOrderController::class)->group(function() {
        Route::get('/pos-list', 'getPOs');
        Route::post('/save-purchase-order', 'store');
        Route::put('/receive-po', 'receivePO');
        Route::put('/update-received-po', 'updateReceivedPO');
        Route::put('/update-pending-po', 'updatePendingPO');
        Route::put('/cancel-po', 'cancelPO');
        Route::put('/return-po', 'returnPO');
        Route::get('/po-dtls-pending', 'getPODtlsPending');
        Route::get('/po-dtls-received', 'getPODtlsReceived');
    });

    Route::controller(SaleController::class)->group(function() {
        Route::get('/sales-list', 'getSales');
        Route::post('/save-sales', 'store');
        Route::get('/sale-dtls', 'getSalesDtls');
        Route::put('/update-sale', 'update');
        Route::put('/cancel-sale', 'cancelSale');
        Route::put('/pay-sale', 'paySale');
        Route::put('/return-sale', 'returnSale');
    });

    Route::controller(SupplierController::class)->group(function() {
        Route::get('/suppliers-list', 'getSuppliers');
        Route::post('/add-supplier', 'store');
        Route::put('/update-supplier/{id}', 'update');
        Route::delete('/delete-supplier/{id}', 'destroy');
    });

    Route::controller(CustomerController::class)->group(function() {
        Route::get('/customers-list', 'getCustomers');
        Route::post('/add-customer', 'store');
        Route::put('/update-customer/{id}', 'update');
        Route::delete('/delete-customer/{id}', 'destroy');
    });

    Route::controller(ColorController::class)->group(function () {
        Route::get('/colors-list', 'getColors');
        Route::put('/set-color', 'setColor');
    });

    Route::middleware('isAdmin')->group(function() {
        Route::get('/users-list', function() {
            return User::all()->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ];
            });
        });
    });
});

