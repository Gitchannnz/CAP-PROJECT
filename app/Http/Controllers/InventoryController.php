<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Sale;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function stockIn(Request $request, $id)
    {
        if (!Gate::any(['is-admin', 'is-staff'])) {
            abort(403, 'This action is unauthorized.');
        }
    
        $product = Product::findOrFail($id);
    
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
    
        $stockInQuantity = $request->quantity;
    
        Inventory::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'stockin_quantity' => $stockInQuantity,
            'created_at' => now(),
        ]);
    
        $product->quantity += $stockInQuantity;
        $product->save();
    
        $notification = Notification::where('related_id', $product->id)
            ->where('type', 'product')
            ->where('is_read', false)
            ->first();
    
        if ($notification) {
            $notification->update(['is_read' => true]);
        }
    
        return response()->json(['success' => true, 'message' => 'Stock updated successfully!']);
    }
    

    public function undoStockIn($id)
    {
        $inventoryRecord = Inventory::where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($inventoryRecord) {
            if ($inventoryRecord->created_at->isToday()) {
                $product = Product::findOrFail($id);
                $product->quantity -= $inventoryRecord->stockin_quantity;
                $product->save();
    
                $inventoryRecord->delete();
    
                return response()->json(['success' => true, 'message' => 'Last stock-in operation undone successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'The last stock-in operation was not performed.']);
            }
        }
        
    
        return response()->json(['success' => false, 'message' => 'No stock-in record found to undo.']);
    }
    
}
