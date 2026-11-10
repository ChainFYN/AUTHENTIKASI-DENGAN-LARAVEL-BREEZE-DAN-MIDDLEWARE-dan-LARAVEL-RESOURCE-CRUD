<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportController extends Controller
{
    public function generateReport()
    {
        $data = [
            'title' => 'Laporan Stok Produk Minimarket',
            'date' => date('d-m-Y'),
            'products' => [
                ['name' => 'Susu Kaleng', 'category' => 'Minuman', 'stock' => 50, 'price' => 15000],
                ['name' => 'Roti Tawar', 'category' => 'Makanan', 'stock' => 20, 'price' => 12000],
                ['name' => 'Sabun Cuci', 'category' => 'Peralatan', 'stock' => 100, 'price' => 5000],
                ['name' => 'Minyak Goreng', 'category' => 'Bahan Pokok', 'stock' => 30, 'price' => 25000],
            ]
        ];

        $pdf = Pdf::loadView('reports.product-report', $data);
        
        // return $pdf->stream('laporan-produk.pdf'); // Untuk tampil di browser
        return $pdf->download('laporan-produk.pdf'); // Untuk langsung download
    }

    public function previewReport()
    {
        $data = [
            'title' => 'Laporan Stok Produk Minimarket',
            'date' => date('d-m-Y'),
            'products' => [
                ['name' => 'Susu Kaleng', 'category' => 'Minuman', 'stock' => 50, 'price' => 15000],
                ['name' => 'Roti Tawar', 'category' => 'Makanan', 'stock' => 20, 'price' => 12000],
                ['name' => 'Sabun Cuci', 'category' => 'Peralatan', 'stock' => 100, 'price' => 5000],
                ['name' => 'Minyak Goreng', 'category' => 'Bahan Pokok', 'stock' => 30, 'price' => 25000],
            ]
        ];

        return view('reports.product-report', $data);
    }
}
