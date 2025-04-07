<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $sales = Transaction::with(['items.item']) // eager load items and each item's details
                ->latest()
                ->simplePaginate(9);

        return view('sales.index', [
            'sales' => $sales
    ]);
    }
}
