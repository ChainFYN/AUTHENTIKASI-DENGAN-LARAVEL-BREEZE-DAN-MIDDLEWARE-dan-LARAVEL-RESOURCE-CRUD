@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Upload File</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Metode 1: Upload dengan Nama Default</h2>
            <form method="POST" action="{{ route('file.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih File</label>
                    <input type="file" name="document" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <p class="text-gray-600 text-xs mt-1">Format: jpg, png, pdf, doc, docx. Max: 2MB</p>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Upload File
                </button>
            </form>
        </div>

        <div class="bg-white rounded shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Metode 2: Upload dengan Nama Custom</h2>
            <form method="POST" action="{{ route('file.upload.custom') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih File</label>
                    <input type="file" name="file" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <p class="text-gray-600 text-xs mt-1">Nama file akan ditambah timestamp</p>
                </div>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Upload dengan Custom Name
                </button>
            </form>
        </div>

        <div class="bg-white rounded shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">File Tersimpan</h2>
            <a href="{{ route('file.list') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                Lihat Daftar File
            </a>
        </div>
    </div>
</div>
@endsection
