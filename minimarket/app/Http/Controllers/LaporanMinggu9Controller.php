<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanMinggu9Controller extends Controller
{
    public function generatePDF()
    {
        $htmlFile = base_path('LAPORAN_FINAL_MINGGU_9.html');
        
        if (!file_exists($htmlFile)) {
            return back()->with('error', 'File laporan tidak ditemukan');
        }
        
        $html = file_get_contents($htmlFile);
        
        $pdf = Pdf::loadHTML($html)
                  ->setPaper('a4', 'portrait')
                  ->setOption('isHtml5ParserEnabled', true)
                  ->setOption('isRemoteEnabled', true);
        
        return $pdf->download('LAPORAN_MINGGU_9.pdf');
    }

    public function previewPDF()
    {
        $htmlFile = base_path('LAPORAN_FINAL_MINGGU_9.html');
        
        if (!file_exists($htmlFile)) {
            return back()->with('error', 'File laporan tidak ditemukan');
        }
        
        return view('laporan-minggu9-preview');
    }
}


