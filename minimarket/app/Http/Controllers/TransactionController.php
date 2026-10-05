<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->view('kasir.dashboard', [
            'module' => 'Transactions'
        ]);
    }

    public function create()
    {
        return response()->view('kasir.dashboard', [
            'module' => 'Create Transaction'
        ]);
    }

    public function store(Request $request)
    {
        return redirect()->route('transactions.index');
    }
}
