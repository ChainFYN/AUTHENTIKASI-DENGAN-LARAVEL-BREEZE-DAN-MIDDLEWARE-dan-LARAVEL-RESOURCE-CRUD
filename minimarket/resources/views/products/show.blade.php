<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-indigo-700">Detail Product</h1>
                        <a href="{{ route('products.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Kembali</a>
                    </div>

                    <table class="w-full table-auto border-collapse">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left w-48">Nama</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $product->name }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Kategori</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $product->category ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Deskripsi</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $product->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Harga</th>
                            <td class="border border-gray-300 px-4 py-2">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Stock</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $product->stock }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Status</th>
                            <td class="border border-gray-300 px-4 py-2">
                                @if($product->is_active)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Aktif</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 bg-gray-100 text-left">Dibuat</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $product->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
