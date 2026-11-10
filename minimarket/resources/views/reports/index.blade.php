@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Laporan PDF</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Preview Laporan</h2>
                <p class="text-gray-600 mb-4">Lihat laporan dalam format HTML sebelum didownload</p>
                <a href="{{ route('report.preview') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Preview
                </a>
            </div>

            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Download PDF</h2>
                <p class="text-gray-600 mb-4">Download laporan dalam format PDF</p>
                <a href="{{ route('report.download') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Download PDF
                </a>
            </div>
        </div>

        <div class="bg-white rounded shadow-lg p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Informasi</h2>
            <ul class="list-disc list-inside text-gray-700">
                <li>Laporan menampilkan daftar produk dengan stok dan harga</li>
                <li>Format PDF memudahkan untuk distribusi dan arsip</li>
                <li>Laporan otomatis menambahkan tanggal cetak</li>
            </ul>
        </div>
    </div>
</div>
@endsection
