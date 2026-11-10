@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Daftar Gambar</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4">
            <a href="{{ route('image.form') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Kembali ke Form Manipulasi
            </a>
        </div>

        <div class="bg-white rounded shadow-lg p-6">
            @if (count($images) > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($images as $image)
                        <div class="border rounded overflow-hidden">
                            <img src="{{ asset('storage/images/' . $image) }}" class="w-full h-48 object-cover">
                            <div class="p-3">
                                <p class="text-sm truncate mb-2" title="{{ $image }}">{{ $image }}</p>
                                <form method="POST" action="{{ route('image.delete', $image) }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-xs" onclick="return confirm('Yakin ingin menghapus?')">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center">Belum ada gambar yang diproses.</p>
            @endif
        </div>
    </div>
</div>
@endsection
