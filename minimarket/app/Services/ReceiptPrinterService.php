<?php

namespace App\Services;

use App\Models\Transaction;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Illuminate\Support\Facades\Log;

class ReceiptPrinterService
{
    protected string $driver;
    protected string $printerName;

    public function __construct()
    {
        $this->driver = config('printing.driver', env('THERMAL_PRINT_DRIVER', 'file'));
        $this->printerName = config('printing.printer_name', env('THERMAL_PRINTER_NAME', 'POS-58'));
    }

    /**
     * Dapatkan konektor printer: FilePrintConnector (simulasi tanpa printer fisik) atau WindowsPrintConnector
     */
    protected function getConnector(string $filename = 'struk_terbaru.txt', bool $forceHardware = false): array
    {
        // Hanya coba koneksi hardware Windows jika driver memang 'windows' DAN di-request hardware
        if ($forceHardware || $this->driver === 'windows') {
            try {
                $connector = new WindowsPrintConnector($this->printerName);
                return [$connector, 'windows_hardware', null];
            } catch (\Throwable $e) {
                Log::warning('WindowsPrintConnector gagal, beralih ke simulasi file: ' . $e->getMessage());
            }
        }

        $receiptDir = config('printing.storage_path', storage_path('app/receipts'));
        if (!file_exists($receiptDir)) {
            mkdir($receiptDir, 0777, true);
        }
        $filePath = $receiptDir . '/' . $filename;
        $connector = new FilePrintConnector($filePath);

        return [$connector, 'simulation_file', $filePath];
    }

    /**
     * ACARA 36: Studi Kasus Toko "Kopi Kita"
     * Format struk minimal: Nama toko, Tanggal/Waktu, Item (Qty & Harga), Total Bayar, Diskon
     */
    public function printKopiKitaReceipt(array $orderData, bool $forceHardware = false): array
    {
        $filename = 'struk_kopi_kita_' . time() . '.txt';
        [$connector, $mode, $filePath] = $this->getConnector($filename, $forceHardware);

        try {
            $printer = new Printer($connector);

            // 1. Header Toko
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("KOPI KITA\n");
            $printer->setEmphasis(false);
            $printer->text("Jl. Danau Toba No. 45, Jember\n");
            $printer->text("Telp: 0812-9876-5432\n");
            $printer->text(str_repeat("=", 32) . "\n");

            // 2. Info Transaksi (Tanggal & Waktu)
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No Struk : " . ($orderData['order_id'] ?? ('KK-' . time())) . "\n");
            $printer->text("Kasir    : " . ($orderData['cashier'] ?? 'Kasir 1') . "\n");
            $printer->text("Waktu    : " . ($orderData['date_time'] ?? now()->format('d/m/Y H:i:s')) . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            // 3. Daftar Item, Kuantitas, dan Harga
            $subtotal = 0;
            $items = $orderData['items'] ?? [];
            foreach ($items as $item) {
                $name = $item['name'];
                $qty = (int) $item['qty'];
                $price = (float) $item['price'];
                $itemSubtotal = $qty * $price;
                $subtotal += $itemSubtotal;

                $printer->text(str_pad(substr($name, 0, 32), 32) . "\n");
                $calcText = "  " . $qty . " x " . number_format($price, 0, ',', '.');
                $subtotalText = number_format($itemSubtotal, 0, ',', '.');
                $spaces = max(1, 32 - strlen($calcText) - strlen($subtotalText));
                $printer->text($calcText . str_repeat(" ", $spaces) . $subtotalText . "\n");
            }

            $printer->text(str_repeat("-", 32) . "\n");

            // 4. Perhitungan Diskon & Total Pembayaran
            $discount = (float) ($orderData['discount'] ?? 0);
            $grandTotal = max(0, $subtotal - $discount);
            $paidAmount = (float) ($orderData['paid_amount'] ?? $grandTotal);
            $changeAmount = max(0, $paidAmount - $grandTotal);

            $this->printTwoColumn($printer, "Subtotal", "Rp" . number_format($subtotal, 0, ',', '.'));
            if ($discount > 0) {
                $this->printTwoColumn($printer, "Diskon", "-Rp" . number_format($discount, 0, ',', '.'));
            }

            $printer->setEmphasis(true);
            $this->printTwoColumn($printer, "TOTAL BAYAR", "Rp" . number_format($grandTotal, 0, ',', '.'));
            $printer->setEmphasis(false);

            $this->printTwoColumn($printer, "Tunai/Bayar", "Rp" . number_format($paidAmount, 0, ',', '.'));
            $this->printTwoColumn($printer, "Kembalian", "Rp" . number_format($changeAmount, 0, ',', '.'));

            $printer->text(str_repeat("=", 32) . "\n");

            // 5. Footer & Barcode/QR
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima Kasih atas Kunjungan Anda!\n");
            $printer->text("Nikmati Harimu Bersama Kopi Kita\n");
            $printer->feed();

            try {
                $printer->qrCode($orderData['order_id'] ?? 'KOPIKITA-' . time(), Printer::QR_ECLEVEL_L, 3);
                $printer->feed();
            } catch (\Throwable $e) {
                // Abaikan jika printer tidak support grafik
            }

            $printer->cut();
            $printer->close();

            return [
                'success' => true,
                'mode' => $mode,
                'file' => $filePath ? str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath) : null,
                'message' => $mode === 'windows_hardware'
                    ? 'Struk Kopi Kita berhasil dicetak ke printer ' . $this->printerName
                    : 'Struk Kopi Kita berhasil dicetak (Mode Simulasi ESC/POS tersimpan di file)'
            ];
        } catch (\Throwable $e) {
            Log::warning('Gagal mencetak struk Kopi Kita: ' . $e->getMessage());

            // Fallback non-rekursif langsung ke FilePrintConnector
            if ($mode === 'windows_hardware') {
                try {
                    $receiptDir = config('printing.storage_path', storage_path('app/receipts'));
                    $fallbackPath = $receiptDir . '/struk_kopi_kita_fallback_' . time() . '.txt';
                    $fallbackConn = new FilePrintConnector($fallbackPath);
                    $fallbackPrinter = new Printer($fallbackConn);
                    $fallbackPrinter->text("KOPI KITA (SIMULASI)\n");
                    $fallbackPrinter->text("Total: Rp" . number_format($grandTotal ?? 0, 0, ',', '.') . "\n");
                    $fallbackPrinter->cut();
                    $fallbackPrinter->close();

                    return [
                        'success' => true,
                        'mode' => 'simulation_file',
                        'file' => str_replace(base_path() . DIRECTORY_SEPARATOR, '', $fallbackPath),
                        'message' => 'Struk Kopi Kita dicetak ke file fallback'
                    ];
                } catch (\Throwable $ex) {
                    // Abaikan
                }
            }

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Cetak Struk Transaksi POS Minimarket secara otomatis dari Model Transaksi
     */
    public function printPosTransaction(Transaction $transaction, bool $forceHardware = false): array
    {
        $transaction->loadMissing(['details', 'user']);
        $filename = 'struk_pos_' . $transaction->transaction_number . '.txt';
        [$connector, $mode, $filePath] = $this->getConnector($filename, $forceHardware);

        try {
            $printer = new Printer($connector);

            // Header Toko Minimarket
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("MINIMARKET POLIJE\n");
            $printer->setEmphasis(false);
            $printer->text("Gedung JTI Polije, Jember\n");
            $printer->text("Telp: (0331) 333532\n");
            $printer->text(str_repeat("=", 32) . "\n");

            // Info Transaksi
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No. Trx : " . $transaction->transaction_number . "\n");
            $printer->text("Kasir   : " . ($transaction->user->name ?? 'Kasir') . "\n");
            $printer->text("Waktu   : " . $transaction->created_at->format('d/m/Y H:i:s') . "\n");
            $printer->text("Metode  : " . $transaction->payment_method . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            // Detail Produk
            foreach ($transaction->details as $item) {
                $printer->text(str_pad(substr($item->product_name, 0, 32), 32) . "\n");
                $calc = "  " . $item->qty . " x " . number_format((float) $item->price, 0, ',', '.');
                $sub = number_format((float) $item->subtotal, 0, ',', '.');
                $sp = max(1, 32 - strlen($calc) - strlen($sub));
                $printer->text($calc . str_repeat(" ", $sp) . $sub . "\n");
            }

            $printer->text(str_repeat("-", 32) . "\n");

            // Ringkasan Pembayaran
            $this->printTwoColumn($printer, "Subtotal", "Rp" . number_format((float) $transaction->subtotal, 0, ',', '.'));
            if ($transaction->discount > 0) {
                $this->printTwoColumn($printer, "Diskon", "-Rp" . number_format((float) $transaction->discount, 0, ',', '.'));
            }
            if ($transaction->tax > 0) {
                $this->printTwoColumn($printer, "Pajak", "Rp" . number_format((float) $transaction->tax, 0, ',', '.'));
            }

            $printer->setEmphasis(true);
            $this->printTwoColumn($printer, "GRAND TOTAL", "Rp" . number_format((float) $transaction->grand_total, 0, ',', '.'));
            $printer->setEmphasis(false);

            $this->printTwoColumn($printer, "Bayar", "Rp" . number_format((float) $transaction->paid_amount, 0, ',', '.'));
            $this->printTwoColumn($printer, "Kembalian", "Rp" . number_format((float) $transaction->change_amount, 0, ',', '.'));

            $printer->text(str_repeat("=", 32) . "\n");

            // Footer
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima Kasih atas Kunjungan Anda!\n");
            $printer->text("Barang yang dibeli tidak dapat ditukar\n");
            $printer->feed();

            $printer->cut();
            $printer->close();

            return [
                'success' => true,
                'mode' => $mode,
                'file' => $filePath ? str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath) : null,
                'message' => 'Struk POS transaksi ' . $transaction->transaction_number . ' berhasil dicetak otomatis'
            ];
        } catch (\Throwable $e) {
            Log::warning('Gagal cetak otomatis POS: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper perataan teks 2 kolom (Kiri dan Kanan) untuk lebar kertas 58mm (32 kolom)
     */
    protected function printTwoColumn(Printer $printer, string $left, string $right, int $width = 32): void
    {
        $spaces = max(1, $width - strlen($left) - strlen($right));
        $printer->text($left . str_repeat(" ", $spaces) . $right . "\n");
    }
}
