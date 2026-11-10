<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Services\ReceiptPrinterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'discount_percent' => 'sometimes|numeric|min:0|max:100',
            'discount_amount' => 'sometimes|numeric|min:0',
            'tax' => 'sometimes|numeric|min:0',
            'other_fee' => 'sometimes|numeric|min:0',
            'payment_method' => 'required|in:Tunai,QRIS,Debit',
        ]);

        $transaction = DB::transaction(function () use ($validated) {
            $quantities = collect($validated['items'])
                ->groupBy('product_id')
                ->map(fn ($items) => $items->sum('qty'));
            $products = Product::query()
                ->whereIn('id', $quantities->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $quantities->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu produk sudah tidak tersedia.',
                ]);
            }

            $subtotal = 0;
            foreach ($quantities as $productId => $qty) {
                $product = $products->get($productId);
                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => "Produk {$product->name} sudah tidak aktif.",
                    ]);
                }
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi. Stok tersedia: {$product->stock}.",
                    ]);
                }

                $subtotal += round((float) $product->price * $qty, 2);
            }

            $discount = min(
                $subtotal,
                round(
                    $subtotal * ((float) ($validated['discount_percent'] ?? 0) / 100)
                    + (float) ($validated['discount_amount'] ?? 0),
                    2
                )
            );
            $tax = (float) ($validated['tax'] ?? 0);
            $otherFee = (float) ($validated['other_fee'] ?? 0);
            $grandTotal = round(max(0, $subtotal - $discount + $tax + $otherFee), 2);
            $paidAmount = (float) $validated['paid_amount'];

            if ($paidAmount < $grandTotal) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Uang pembayaran masih kurang dari total transaksi.',
                ]);
            }

            $transaction = Transaction::create([
                'transaction_number' => 'TRX-' . now()->format('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4))),
                'user_id' => auth()->id(),
                'customer_id' => null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'other_fee' => $otherFee,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => round($paidAmount - $grandTotal, 2),
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
            ]);

            foreach ($quantities as $productId => $qty) {
                $product = $products->get($productId);
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_code' => $product->code ?? ('PRD' . $product->id),
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $qty,
                    'discount' => 0,
                    'subtotal' => round((float) $product->price * $qty, 2),
                ]);

                $product->decrement('stock', $qty);
            }

            return $transaction;
        });

        // Broadcast event real-time setelah transaksi berhasil
        event(new OrderPlaced($transaction, auth()->user()));

        // Acara 36: Cetak struk otomatis ke printer thermal ESC/POS setelah transaksi selesai
        $printResult = null;
        if ($request->boolean('auto_print', true)) {
            try {
                $printerService = app(ReceiptPrinterService::class);
                $forceHardware = $request->boolean('print_hardware', false);
                $printResult = $printerService->printPosTransaction($transaction, $forceHardware);
            } catch (\Throwable $e) {
                Log::warning('Gagal cetak otomatis receipt: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'transaction_number' => $transaction->transaction_number,
            'grand_total' => $transaction->grand_total,
            'change_amount' => $transaction->change_amount,
            'receipt' => $printResult,
        ]);
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

    /**
     * Acara 36: Cetak ulang struk thermal ESC/POS untuk transaksi tertentu
     */
    public function printThermalReceipt(Request $request, $id)
    {
        $transaction = Transaction::with(['details', 'user'])->findOrFail($id);
        $printerService = app(ReceiptPrinterService::class);
        $forceHardware = $request->query('hardware') === '1';
        $result = $printerService->printPosTransaction($transaction, $forceHardware);

        return response()->json($result);
    }
}
