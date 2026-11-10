@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Image Manipulation</h1>

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
                @if (session('filename'))
                    <br><img src="{{ asset('storage/images/' . session('filename')) }}" class="mt-2 max-w-xs rounded shadow">
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Resize Image</h2>
                <form method="POST" action="{{ route('image.resize') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Gambar</label>
                        <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Width (px)</label>
                        <input type="number" name="width" value="300" min="50" max="1000" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Height (px)</label>
                        <input type="number" name="height" value="300" min="50" max="1000" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Resize
                    </button>
                </form>
            </div>

            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Crop Image</h2>
                <form method="POST" action="{{ route('image.crop') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Gambar</label>
                        <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1">Width</label>
                            <input type="number" name="width" value="200" min="50" max="500" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1">Height</label>
                            <input type="number" name="height" value="200" min="50" max="500" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1">X Position</label>
                            <input type="number" name="x" value="0" min="0" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-xs font-bold mb-1">Y Position</label>
                            <input type="number" name="y" value="0" min="0" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Crop
                    </button>
                </form>
            </div>

            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Rotate Image</h2>
                <form method="POST" action="{{ route('image.rotate') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Gambar</label>
                        <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Angle (derajat)</label>
                        <input type="number" name="angle" value="45" min="0" max="360" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                        <p class="text-gray-500 text-xs mt-1">0-360 derajat</p>
                    </div>
                    <button type="submit" class="w-full bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                        Rotate
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Gambar Tersimpan</h2>
            <a href="{{ route('image.list') }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                Lihat Daftar Gambar
            </a>
        </div>
    </div>
</div>
@endsection
