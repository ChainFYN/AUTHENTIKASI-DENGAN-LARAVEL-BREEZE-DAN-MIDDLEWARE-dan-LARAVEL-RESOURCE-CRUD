<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$clientKey = config('midtrans.clientKey');
        Config::$isProduction = (bool) config('midtrans.isProduction');
        Config::$isSanitized = (bool) config('midtrans.isSanitized');
        Config::$is3ds = (bool) config('midtrans.is3ds');
        Config::$curlOptions = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [],
        ];
    }

    /**
     * Halaman Demo Checkout Produk Digital (Acara 38)
     */
    public function index()
    {
        $products = [
            [
                'id' => 'DIG-01',
                'name' => 'E-Book Panduan Mahir Laravel 11 & ESC-POS',
                'price' => 50000,
                'description' => 'E-book lengkap teknik pencetakan printer thermal dan integrasi payment gateway di Laravel.',
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&q=80',
            ],
            [
                'id' => 'DIG-02',
                'name' => 'Template Dashboard POS Minimarket Modern',
                'price' => 75000,
                'description' => 'Source code UI kasir responsif siap pakai terintegrasi Tailwind CSS dan Alpine.js.',
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&q=80',
            ],
            [
                'id' => 'DIG-03',
                'name' => 'Voucher Lisensi Software Kasir Multi-User',
                'price' => 120000,
                'description' => 'Lisensi resmi penggunaan sistem POS untuk toko retail dan kedai kopi.',
                'image' => 'https://images.unsplash.com/photo-1556742049-0a67e557224f?w=400&q=80',
            ],
        ];

        $recentOrders = Order::latest()->take(5)->get();

        return view('midtrans.checkout', compact('products', 'recentOrders'));
    }

    /**
     * ACARA 38: Prosedur Kerja 2 - Method Checkout
     * Menghasilkan Snap Token untuk frontend checkout
     */
    public function checkout(Request $request)
    {
        $orderId = 'ORDER-' . time() . '-' . rand(100, 999);
        $price = (float) $request->input('price', 50000);
        $productName = $request->input('product_name', 'Produk Digital Workshop');
        $customerName = $request->input('customer_name', 'John Doe');
        $customerEmail = $request->input('customer_email', 'john@example.com');

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $price,
            ],
            'item_details' => [
                [
                    'id' => $request->input('product_id', 'PROD-01'),
                    'price' => (int) $price,
                    'quantity' => 1,
                    'name' => substr($productName, 0, 50),
                ]
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
            ]
        ];

        try {
            // Cek apakah kredensial Midtrans valid
            $serverKey = config('midtrans.serverKey');
            $isDummyKey = empty($serverKey) || str_contains($serverKey, 'YOUR_SERVER_KEY') || str_contains($serverKey, 'XXXXXX');

            if ($isDummyKey) {
                // Token simulasi jika user belum memasukkan Server Key Sandbox pribadinya
                $snapToken = 'SANDBOX_MOCK_TOKEN_' . md5($orderId);
            } else {
                $snapToken = Snap::getSnapToken($params);
            }

            // Simpan status pesanan di database sesuai Acara 38 Hal 21
            $order = Order::create([
                'order_id' => $orderId,
                'status' => 'pending',
                'gross_amount' => $price,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'snap_token' => $snapToken,
                'payload' => $params,
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'is_simulated' => $isDummyKey,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());

            // Fallback graceful jika API Midtrans offline/kredensial sandbox ditolak
            $mockToken = 'SANDBOX_MOCK_TOKEN_' . md5($orderId);
            $order = Order::create([
                'order_id' => $orderId,
                'status' => 'pending',
                'gross_amount' => $price,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'snap_token' => $mockToken,
                'payload' => $params,
            ]);

            return response()->json([
                'snap_token' => $mockToken,
                'order_id' => $orderId,
                'is_simulated' => true,
                'warning' => 'Menggunakan Snap Token simulasi: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * ACARA 38: Prosedur Kerja 2 - Method Webhook Notification
     * Menangani callback status dari Midtrans
     */
    public function notification(Request $request)
    {
        try {
            $transaction = null;
            $order_id = null;
            $payment_type = null;
            $fraud = null;
            $payload = [];

            // Coba parsing via SDK Midtrans\Notification
            try {
                $notif = new Notification();
                $transaction = $notif->transaction_status;
                $order_id = $notif->order_id;
                $payment_type = $notif->payment_type ?? null;
                $fraud = $notif->fraud_status ?? null;
                $payload = (array) $notif->getResponse();
            } catch (\Exception $e) {
                // Fallback parsing langsung dari Request payload (berguna untuk pengujian via curl/postman/test)
                $transaction = $request->input('transaction_status');
                $order_id = $request->input('order_id');
                $payment_type = $request->input('payment_type');
                $fraud = $request->input('fraud_status');
                $payload = $request->all();
            }

            if (!$order_id) {
                return response()->json(['error' => 'Order ID is required'], 400);
            }

            // Cari order berdasarkan order_id
            $order = Order::where('order_id', $order_id)->first();
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Logika status sesuai petunjuk Acara 38 Halaman 18
            $newStatus = 'pending';

            if ($transaction == 'capture') {
                if ($fraud == 'challenge') {
                    $newStatus = 'challenge';
                } else if ($fraud == 'accept') {
                    $newStatus = 'PAID';
                }
            } elseif ($transaction == 'settlement') {
                // Update status pesanan di database jadi "PAID"
                $newStatus = 'PAID';
            } elseif ($transaction == 'pending') {
                // Status pending
                $newStatus = 'pending';
            } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                // Gagal bayar
                $newStatus = 'FAILED';
            }

            // Simpan status transaksi ke database (Acara 38 Hal 21)
            $order->status = $newStatus;
            $order->transaction_status = $transaction;
            $order->payment_type = $payment_type;
            $order->payload = $payload;
            $order->save();

            return response()->json([
                'message' => 'Notification handled',
                'order_id' => $order_id,
                'status' => $newStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('Webhook notification error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Endpoint untuk memeriksa status pesanan dari frontend
     */
    public function orderStatus($orderId)
    {
        $order = Order::where('order_id', $orderId)->firstOrFail();

        return response()->json([
            'order_id' => $order->order_id,
            'status' => $order->status,
            'transaction_status' => $order->transaction_status,
            'gross_amount' => $order->gross_amount,
            'customer_name' => $order->customer_name,
            'created_at' => $order->created_at->format('d/m/Y H:i:s'),
        ]);
    }
}

