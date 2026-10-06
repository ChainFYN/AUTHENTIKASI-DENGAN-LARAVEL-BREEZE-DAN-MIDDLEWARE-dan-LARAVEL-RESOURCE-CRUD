<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        return view('kasir.transaksi');
    }

    public function create()
    {
        return view('kasir.transaksi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'other_fee' => 'numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'change_amount' => 'numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $transactionNumber = 'TRX-' . date('Ymd') . '-' . str_pad((int) DB::table('transactions')->whereDate('created_at', now())->count() + 1, 3, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'user_id' => auth()->id(),
                'customer_id' => null,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'tax' => $request->tax,
                'other_fee' => $request->other_fee,
                'grand_total' => $request->grand_total,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->change_amount,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_code' => $product->code ?? ('PRD' . $product->id),
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $item['qty'],
                    'discount' => 0,
                    'subtotal' => $product->price * $item['qty'],
                ]);

                $product->decrement('stock', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'transaction_number' => $transactionNumber,
                'grand_total' => $transaction->grand_total,
                'change_amount' => $transaction->change_amount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function searchProduct(Request $request)
    {
        $keyword = $request->input('q');

        $products = Product::where('is_active', true)
            ->where(function($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('category', 'LIKE', "%{$keyword}%");
            })
            ->get(['id', 'code', 'name', 'category', 'price', 'stock', 'image']);

        return response()->json($products);
    }

    public function getCustomers()
    {
        $customers = Customer::all(['id', 'name', 'phone', 'member_code']);
        return response()->json($customers);
    }
}
