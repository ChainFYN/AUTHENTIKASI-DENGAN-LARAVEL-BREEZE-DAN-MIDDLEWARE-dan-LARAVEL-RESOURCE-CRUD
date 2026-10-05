<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Product</label>
    <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror" value="{{ old('name', $product->name ?? '') }}">
    @error('name')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
    <input type="text" name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('category', $product->category ?? '') }}">
</div>

<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
    <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="4">{{ old('description', $product->description ?? '') }}</textarea>
</div>

<div class="grid grid-cols-2 gap-4">
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Harga</label>
        <input type="number" name="price" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('price') border-red-500 @enderror" value="{{ old('price', $product->price ?? '') }}">
        @error('price')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2">Stock</label>
        <input type="number" name="stock" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('stock', $product->stock ?? 0) }}">
    </div>
</div>

<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">Image</label>
    <input type="text" name="image" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('image', $product->image ?? '') }}" placeholder="nama-file.jpg">
</div>

<div class="mb-4">
    <label class="flex items-center">
        <input type="checkbox" name="is_active" value="1" class="mr-2" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-gray-700 text-sm font-bold">Product Aktif</span>
    </label>
</div>
