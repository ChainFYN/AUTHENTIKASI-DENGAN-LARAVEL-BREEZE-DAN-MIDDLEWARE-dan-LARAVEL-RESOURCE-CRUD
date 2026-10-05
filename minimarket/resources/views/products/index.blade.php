<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-indigo-700">Products</h1>
                            <p class="text-gray-500 text-sm">Daftar produk</p>
                        </div>
                        <a href="{{ route('products.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                            + Tambah Product
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2 text-left">No</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Product</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Kategori</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Harga</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Stock</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ $products->firstItem() + $loop->index }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->description)
                                                <br>
                                                <small class="text-gray-500">
                                                    {{ \Illuminate\Support\Str::limit($product->description, 50) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ $product->category ?? '-' }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            {{ $product->stock }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            @if($product->is_active)
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Aktif</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <div class="flex gap-1">
                                                <a href="{{ route('products.show', $product) }}" class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600">Detail</a>
                                                <a href="{{ route('products.edit', $product) }}" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs hover:bg-yellow-600">Edit</a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus product ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                                            Belum ada data product.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
