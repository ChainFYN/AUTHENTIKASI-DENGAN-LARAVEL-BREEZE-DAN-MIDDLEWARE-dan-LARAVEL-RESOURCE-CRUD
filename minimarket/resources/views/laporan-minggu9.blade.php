@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Laporan Praktikum Minggu 9</h1>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-blue-700"><strong>📋 Dokumentasi Lengkap:</strong> File Storage, PDF Report, Image Manipulation, Email</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Preview Laporan</h2>
                <p class="text-gray-600 mb-4">Lihat laporan dalam format HTML di browser</p>
                <a href="{{ route('laporan.minggu9.preview') }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Preview HTML
                </a>
            </div>

            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Download PDF</h2>
                <p class="text-gray-600 mb-4">Download laporan lengkap dalam format PDF</p>
                <a href="{{ route('laporan.minggu9.download') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Download PDF
                </a>
            </div>
        </div>

        <div class="bg-white rounded shadow-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Isi Laporan</h2>
            <ul class="list-disc list-inside text-gray-700 space-y-2">
                <li><strong>ACARA 29 - File Storage:</strong> Upload file, manajemen penyimpanan, validasi file</li>
                <li><strong>ACARA 30 - PDF Report:</strong> Generate laporan PDF, template dinamis, preview & download</li>
                <li><strong>ACARA 31 - Image Manipulation:</strong> Resize, crop, rotate gambar, galeri hasil</li>
                <li><strong>ACARA 32 - Email:</strong> Template email, kirim email dinamis, preview template</li>
                <li><strong>Kesimpulan:</strong> Ringkasan pembelajaran dan aplikabilitas industri</li>
            </ul>
        </div>

        <div class="bg-gray-50 rounded shadow-lg p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Struktur Dokumentasi</h2>
            <div class="space-y-3 text-sm text-gray-700">
                <p><strong>1. Screenshot Program:</strong> Struktur folder dan file yang dibuat</p>
                <p><strong>2. Screenshot Hasil:</strong> Interface aplikasi dan hasil praktikum</p>
                <p><strong>3. Langkah-Langkah:</strong> Penjelasan terperinci setiap implementasi</p>
                <p><strong>4. Kesimpulan:</strong> Ringkasan pembelajaran dan skill yang diperoleh</p>
            </div>
        </div>
    </div>
</div>
@endsection
