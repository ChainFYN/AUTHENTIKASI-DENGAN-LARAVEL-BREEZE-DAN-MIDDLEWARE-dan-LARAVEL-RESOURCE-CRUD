<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class ReceiptController extends Controller
{
    /**
     * ACARA 35: Prosedur Kerja 2 & 4 - Cetak Struk Dasar
     * Disesuaikan: Langsung menggunakan mode FilePrintConnector saat tidak ada printer fisik
     */
    public function print(Request $request)
    {
        $driver = config('printing.driver', env('THERMAL_PRINT_DRIVER', 'file'));
        $printerName = config('printing.printer_name', env('THERMAL_PRINTER_NAME', 'POS-58'));

        // Hanya hubungkan ke printer Windows fisik jika dikonfigurasi driver 'windows' dan ada request eksplisit
        if ($driver === 'windows' && $request->query('hardware') === '1') {
            try {
                $connector = new WindowsPrintConnector($printerName);
                $printer = new Printer($connector);

                $printer->text("TOKO CONTOH\n");
                $printer->text("Jl. Mawar No. 123\n");
                $printer->text("========================\n");
                $printer->text("Barang A       2 x 5.000\n");
                $printer->text("Barang B       1 x 10.000\n");
                $printer->text("------------------------\n");
                $printer->text("Total:         Rp20.000\n");
                $printer->text("========================\n");
                $printer->text("Terima kasih!\n");

                $printer->cut();
                $printer->close();

                return response()->json([
                    'success' => true,
                    'mode' => 'windows_hardware',
                    'printer' => $printerName,
                    'message' => 'Struk berhasil dicetak ke printer ' . $printerName
                ]);
            } catch (\Exception $e) {
                // Fallback otomatis jika printer offline
            }
        }

        // Mode Simulasi File (Ideal untuk pengerjaan tanpa printer fisik)
        try {
            $receiptDir = config('printing.storage_path', storage_path('app/receipts'));
            if (!file_exists($receiptDir)) {
                mkdir($receiptDir, 0777, true);
            }
            $filePath = $receiptDir . '/struk_acara35.txt';
            $connector = new FilePrintConnector($filePath);
            $printer = new Printer($connector);

            $printer->text("TOKO CONTOH\n");
            $printer->text("Jl. Mawar No. 123\n");
            $printer->text("========================\n");
            $printer->text("Barang A       2 x 5.000\n");
            $printer->text("Barang B       1 x 10.000\n");
            $printer->text("------------------------\n");
            $printer->text("Total:         Rp20.000\n");
            $printer->text("========================\n");
            $printer->text("Terima kasih!\n");

            $printer->cut();
            $printer->close();

            $outputContent = file_exists($filePath) ? file_get_contents($filePath) : '';

            return response()->json([
                'success' => true,
                'mode' => 'simulation_file',
                'file' => 'storage/app/receipts/struk_acara35.txt',
                'message' => 'Struk berhasil dicetak (Mode Simulasi File - tanpa printer fisik)',
                'preview' => $outputContent
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ACARA 35: Prosedur Kerja 5 - Custom Formatting
     */
    public function customFormatting(Request $request)
    {
        $driver = config('printing.driver', env('THERMAL_PRINT_DRIVER', 'file'));
        $printerName = config('printing.printer_name', env('THERMAL_PRINTER_NAME', 'POS-58'));

        $printCommands = function (Printer $printer) {
            // Bold dan Center
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("TOKO CONTOH\n");
            $printer->setEmphasis(false);
            $printer->text("Jl. Mawar No. 123\n");
            $printer->text("Telp: 08123456789\n");
            $printer->feed();

            // Left Align untuk Item
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(str_repeat("=", 32) . "\n");
            $printer->text("Barang A       2 x 5.000\n");
            $printer->text("Barang B       1 x 10.000\n");
            $printer->text(str_repeat("-", 32) . "\n");

            // Bold Total
            $printer->setEmphasis(true);
            $printer->text("Total:         Rp20.000\n");
            $printer->setEmphasis(false);
            $printer->text(str_repeat("=", 32) . "\n");

            // Underline
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text("Terima kasih atas kunjungan Anda!\n");
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            $printer->feed();

            // QR Code (fitur ESC/POS Mike42)
            try {
                $printer->qrCode("https://polije.ac.id", Printer::QR_ECLEVEL_L, 4);
                $printer->feed();
            } catch (\Exception $ex) {
                // Abaikan jika tidak support grafis
            }

            $printer->cut();
        };

        if ($driver === 'windows' && $request->query('hardware') === '1') {
            try {
                $connector = new WindowsPrintConnector($printerName);
                $printer = new Printer($connector);
                $printCommands($printer);
                $printer->close();

                return response()->json([
                    'success' => true,
                    'mode' => 'windows_hardware',
                    'printer' => $printerName,
                    'message' => 'Custom formatting struk berhasil dicetak ke printer fisik'
                ]);
            } catch (\Exception $e) {
                // Lanjut ke fallback
            }
        }

        try {
            $receiptDir = config('printing.storage_path', storage_path('app/receipts'));
            if (!file_exists($receiptDir)) {
                mkdir($receiptDir, 0777, true);
            }
            $filePath = $receiptDir . '/struk_custom_formatting.txt';
            $connector = new FilePrintConnector($filePath);
            $printer = new Printer($connector);
            $printCommands($printer);
            $printer->close();

            $outputContent = file_exists($filePath) ? file_get_contents($filePath) : '';

            return response()->json([
                'success' => true,
                'mode' => 'simulation_file',
                'file' => 'storage/app/receipts/struk_custom_formatting.txt',
                'message' => 'Custom formatting struk (Bold, Center, Underline, QR) berhasil dicetak (Mode Simulasi File)',
                'preview' => $outputContent
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
