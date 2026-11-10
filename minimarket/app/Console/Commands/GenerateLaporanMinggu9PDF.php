<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateLaporanMinggu9PDF extends Command
{
    protected $signature = 'laporan:minggu9-pdf';
    protected $description = 'Generate PDF from LAPORAN_FINAL_MINGGU_9.html';

    public function handle()
    {
        $html = file_get_contents(base_path('LAPORAN_FINAL_MINGGU_9.html'));
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'portrait');
        $pdf->save(base_path('LAPORAN_FINAL_MINGGU_9.pdf'));
        $this->info('Laporan PDF berhasil dibuat: LAPORAN_FINAL_MINGGU_9.pdf');
    }
}
