<?php

namespace App\Http\Controllers;

use App\Services\ReceiptPrinterService;
use Illuminate\Http\Request;

class KopiKitaController extends Controller
{
    protected ReceiptPrinterService $printerService;

    public function __construct(ReceiptPrinterService $printerService)
    {
        $this->printerService = $printerService;
    }

    /**
     * ACARA 36: Studi Kasus Toko "Kopi Kita"
     * Endpoint untuk mencetak struk contoh pesanan Kopi Kita secara otomatis
     */
    public function printSample(Request $request)
    {
        $forceHardware = $request->query('hardware') === '1';

        $sampleOrder = [
            'order_id' => 'KK-' . date('Ymd') . '-001',
            'cashier' => 'Budi Setiawan',
            'date_time' => now()->format('d/m/Y H:i:s'),
            'items' => [
                ['name' => 'Kopi Susu Gula Aren', 'qty' => 2, 'price' => 18000],
                ['name' => 'Americano Ice', 'qty' => 1, 'price' => 15000],
                ['name' => 'Croissant Cokelat', 'qty' => 2, 'price' => 12500],
            ],
            'discount' => 5000,
            'paid_amount' => 100000,
        ];

        $result = $this->printerService->printKopiKitaReceipt($sampleOrder, $forceHardware);

        return response()->json([
            'study_case' => 'Acara 36 - Toko Kopi Kita',
            'result' => $result,
            'order_summary' => [
                'toko' => 'Kopi Kita',
                'items_count' => count($sampleOrder['items']),
                'subtotal' => 76000,
                'discount' => 5000,
                'total_bayar' => 71000,
                'status' => 'Struk tercetak dari backend PHP (ESC/POS)'
            ]
        ]);
    }

    /**
     * Endpoint POST untuk mencatat transaksi dan langsung mencetak struk otomatis
     */
    public function checkoutAndPrint(Request $request)
    {
        $validated = $request->validate([
            'cashier' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'hardware' => 'nullable|boolean',
        ]);

        $orderData = [
            'order_id' => 'KK-' . date('Ymd') . '-' . rand(100, 999),
            'cashier' => $validated['cashier'] ?? 'Kasir Kopi Kita',
            'date_time' => now()->format('d/m/Y H:i:s'),
            'items' => $validated['items'],
            'discount' => $validated['discount'] ?? 0,
            'paid_amount' => $validated['paid_amount'] ?? 0,
        ];

        $forceHardware = (bool) ($validated['hardware'] ?? false);
        $result = $this->printerService->printKopiKitaReceipt($orderData, $forceHardware);

        return response()->json([
            'success' => $result['success'] ?? false,
            'order_id' => $orderData['order_id'],
            'print_details' => $result,
        ]);
    }
}
