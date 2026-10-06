<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaksi POS Kasir') }}
        </h2>
    </x-slot>

    <div class="pos-container py-6">
        <!-- HEADER -->
        <header class="header flex justify-between items-center p-4 bg-indigo-600 text-white rounded-lg mb-6 shadow">
            <div>
                <div class="store-name text-xl font-bold">TOKO RETAIL MAKMUR</div>
                <div class="store-info text-sm">Jl. Contoh No. 123 | Telp. 0812-xxxx-xxxx</div>
            </div>
            <div class="transaction-info text-right">
                <div class="text-xs">No. Transaksi</div>
                <div class="transaction-number text-lg font-mono font-bold" id="transactionNumber">TRX-{{ date('Ymd') }}-001</div>
                <div class="text-xs" id="currentDate">{{ date('d/m/Y H:i') }}</div>
            </div>
        </header>

        <!-- MAIN -->
        <main class="main flex gap-6">
            <!-- LEFT -->
            <section class="flex-[2]">
                <!-- SEARCH -->
                <div class="card bg-white shadow rounded-lg p-4 mb-4">
                    <div class="font-bold text-gray-700 mb-2">Tambah Barang</div>
                    <div class="search-area flex gap-2">
                        <div class="search-input relative flex-1">
                            <input type="text" id="searchProduct" placeholder="Cari nama barang / kode / barcode..." autocomplete="off" class="w-full p-2 border rounded">
                            <div class="product-results absolute top-full left-0 right-0 bg-white border rounded shadow-lg max-h-60 overflow-y-auto z-50" id="productResults" style="display:none;"></div>
                        </div>
                    </div>
                </div>

                <!-- CART -->
                <div class="card bg-white shadow rounded-lg p-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="font-bold text-gray-700">Keranjang Belanja</span>
                        <span id="itemCount" class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs font-bold">0 Item</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="p-2 text-left">No</th>
                                    <th class="p-2 text-left">Barang</th>
                                    <th class="p-2 text-left">Harga</th>
                                    <th class="p-2 text-center">Qty</th>
                                    <th class="p-2 text-right">Subtotal</th>
                                    <th class="p-2"></th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr>
                                    <td colspan="6" class="text-center p-6 text-gray-400">Keranjang masih kosong.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- RIGHT -->
            <aside class="flex-[1]">
                <div class="card bg-white shadow rounded-lg p-4">
                    <div class="font-bold text-gray-700 mb-4">Ringkasan Pembayaran</div>
                    <div class="flex flex-col gap-3">
                        <div class="flex justify-between bg-gray-50 p-2 rounded">
                            <span>Total Item</span>
                            <strong id="totalQty">0</strong>
                        </div>
                        <div class="flex justify-between bg-gray-50 p-2 rounded">
                            <span>Subtotal</span>
                            <strong id="subtotal">Rp 0</strong>
                        </div>
                        <div class="flex justify-between bg-gray-50 p-2 rounded items-center">
                            <span>Diskon (%)</span>
                            <input type="number" id="discountPercent" value="0" min="0" max="100" onchange="calculateTotal()" class="w-20 p-1 border rounded text-right">
                        </div>
                        <div class="flex justify-between bg-gray-50 p-2 rounded items-center">
                            <span>Diskon (Rp)</span>
                            <input type="number" id="discountAmount" value="0" min="0" onchange="calculateTotal()" class="w-28 p-1 border rounded text-right">
                        </div>
                        <div class="flex justify-between bg-gray-50 p-2 rounded items-center">
                            <span>Pajak / PPN</span>
                            <input type="number" id="tax" value="0" min="0" onchange="calculateTotal()" class="w-28 p-1 border rounded text-right">
                        </div>
                        <div class="flex justify-between bg-gray-50 p-2 rounded items-center">
                            <span>Biaya Lain</span>
                            <input type="number" id="otherFee" value="0" min="0" onchange="calculateTotal()" class="w-28 p-1 border rounded text-right">
                        </div>

                        <div class="bg-green-600 text-white p-4 rounded text-center">
                            <div class="text-xs uppercase">TOTAL AKHIR</div>
                            <div class="text-2xl font-bold" id="grandTotal">Rp 0</div>
                        </div>

                        <div class="mt-2">
                            <label class="block font-medium text-sm mb-1">Uang Dibayar</label>
                            <input type="number" id="payment" class="w-full p-2 border-2 border-indigo-500 rounded text-right text-lg font-bold" placeholder="0" oninput="calculateChange()">
                        </div>

                        <div>
                            <label class="block font-medium text-sm mb-1">Metode Pembayaran</label>
                            <div class="flex gap-1 flex-wrap">
                                <button class="px-3 py-1 border rounded text-xs active bg-indigo-600 text-white" onclick="selectPayment(this)">Tunai</button>
                                <button class="px-3 py-1 border rounded text-xs" onclick="selectPayment(this)">QRIS</button>
                                <button class="px-3 py-1 border rounded text-xs" onclick="selectPayment(this)">Debit</button>
                                <button class="px-3 py-1 border rounded text-xs" onclick="selectPayment(this)">Transfer</button>
                            </div>
                        </div>

                        <div id="changeBox" class="bg-green-500 text-white p-3 rounded text-center">
                            <div class="text-xs uppercase">KEMBALIAN</div>
                            <div class="text-xl font-bold" id="change">Rp 0</div>
                        </div>

                        <div class="flex gap-2 mt-4 pt-2 border-t">
                            <button type="button" class="px-3 py-2 bg-yellow-500 text-white rounded font-bold hover:bg-yellow-600 text-sm" onclick="holdTransaction()">Tahan</button>
                            <button type="button" class="px-3 py-2 bg-red-500 text-white rounded font-bold hover:bg-red-600 text-sm" onclick="cancelTransaction()">Batal</button>
                            <button type="button" class="flex-1 bg-green-600 text-white py-2 rounded font-bold hover:bg-green-700 text-base shadow" style="background-color: #16a34a !important; color: white !important;" onclick="processPayment()">BAYAR & CETAK</button>
                        </div>
                    </div>
                </div>
            </aside>
        </main>
    </div>

    <link rel="stylesheet" href="{{ asset('css/cashier.css') }}">
    <script src="{{ asset('js/cashier.js') }}" defer></script>
    <script>
        // backup function jika JS gagal load
        window.processPayment = function() {
            const total = document.getElementById("grandTotal").innerText.replace(/[^0-9]/g, "") || 0;
            const payment = document.getElementById("payment").value || 0;
            if (payment < total) return alert("Uang kurang");
            alert("Pembayaran berhasil: Rp " + (payment - total).toLocaleString("id-ID"));
        };
        window.holdTransaction = () => alert("Transaksi ditahan");
        window.cancelTransaction = () => confirm("Batal transaksi?") && (window.location.reload());
    </script>
</x-app-layout>