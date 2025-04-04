<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSController extends Controller
{
    public function index()
    {
        // Fetch all inventory items to display in the POS interface
        $items = Item::with('supplier')->latest()->simplePaginate(25);

        // Return the view with the fetched data
        return view('pos.index', [
            'items' => $items
        ]);
    }

    public function create() {
        return view('pos.create');
    }

    public function show(Item $item) {
        return view('pos.show', ['item' => $item]);
    }

    public function store(Request $request)
    {
        // Validate cart data and payment method
        $cart = json_decode($request->input('cart'), true);
        $paymentMethod = $request->input('payment_method');
        
        // Validate if cart is empty
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty!');
        }

        // Validate data
        $request->validate([
            'payment_method' => 'required|string',
            'cart' => 'required|json',
        ]);

        // Create the transaction record
        $transaction = Transaction::create([
            'payment_method' => $paymentMethod,
            'total_amount' => array_sum(array_column($cart, 'price')),
            'status' => 'pending'
        ]);

        // Process each cart item
        foreach ($cart as $item) {
            // Ensure that the item exists in the inventory
            $inventoryItem = Item::find($item['id']);
            
            if ($inventoryItem && $inventoryItem->quantity >= $item['quantity']) {
                // Create the transaction item
                $transactionItem = TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_id' => $item['id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                ]);

                // Reduce the inventory quantity
                $inventoryItem->decrement('quantity', $item['quantity']);
            } else {
                // If there's not enough stock
                return redirect()->back()->with('error', "Not enough stock for item: {$item['name']}");
            }
        }

        // Mark transaction as completed after all items are processed
        $transaction->update(['status' => 'completed']);

        // Redirect to confirmation page with success message
        return redirect('/pos')->with('success', 'Transaction processed successfully');
    }


    public function edit(Item $item) {
        return view('pos.edit', ['item' => $item]);
    }

    public function update(Item $item) {
    
        return redirect('/pos/' . $item->id);
    }

    public function destroy(Item $item) {
        $item->delete();

        return redirect('/pos');
    }
    
}
