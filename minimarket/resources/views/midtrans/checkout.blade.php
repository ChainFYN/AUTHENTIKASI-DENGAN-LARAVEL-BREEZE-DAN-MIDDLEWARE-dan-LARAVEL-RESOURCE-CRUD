<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acara 37 & 38: Midtrans Payment Gateway - Pembelian Produk Digital</title>
    <!-- Tailwind CSS CDN untuk preview mandiri -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Midtrans Snap JS (Sandbox) Sesuai Acara 38 Hal 20 -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen">
    <!-- Navbar -->
    <header class="bg-indigo-600 text-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold tracking-tight">Workshop SI Web Framework</h1>
                <p class="text-xs text-indigo-200">Acara 37 & 38: Payment Gateway Midtrans (Snap API)</p>
            </div>
            <a href="{{ url('/') }}" class="text-sm bg-indigo-700 hover:bg-indigo-800 px-3 py-1.5 rounded-lg transition">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <!-- Notifikasi Banner Konfigurasi -->
        <div class="mb-6 p-4 rounded-xl border bg-white shadow-sm flex items-start gap-3">
            <span class="text-2xl">💳</span>
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Status Gateway: Midtrans Sandbox</h2>
                <p class="text-xs text-slate-600 mt-0.5">
                    Client Key: <code class="bg-slate-100 px-1 py-0.5 rounded text-indigo-600">{{ config('midtrans.clientKey') ?: 'Belum diatur' }}</code> |
                    Production Mode: <code class="bg-slate-100 px-1 py-0.5 rounded">{{ config('midtrans.isProduction') ? 'True (Live)' : 'False (Sandbox)' }}</code>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Produk Digital -->
            <div class="lg:col-span-2 space-y-4">
                <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span>📦</span> Katalog Produk Digital (Studi Kasus)
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($products as $product)
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between hover:border-indigo-300 transition">
                            <div class="p-5">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-indigo-50 text-indigo-600">{{ $product['id'] }}</span>
                                <h3 class="font-bold text-slate-900 mt-2">{{ $product['name'] }}</h3>
                                <p class="text-xs text-slate-500 mt-1">{{ $product['description'] }}</p>
                                <div class="mt-4 text-xl font-extrabold text-emerald-600">
                                    Rp {{ number_format($product['price'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="p-5 pt-0">
                                <button type="button" 
                                        onclick="selectProduct('{{ $product['id'] }}', '{{ $product['name'] }}', {{ $product['price'] }})"
                                        class="w-full py-2 px-3 text-center rounded-lg bg-slate-900 hover:bg-indigo-600 text-white text-sm font-semibold transition">
                                    Pilih Produk
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kolom Form Checkout & Pembayaran (Sesuai Prosedur Acara 38) -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span>🛒</span> Form Checkout
                    </h2>

                    <form id="checkout-form" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Pembeli</label>
                            <input type="text" id="customer-name" value="Budi Pratama" required
                                   class="w-full text-sm px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Email Pembeli</label>
                            <input type="email" id="customer-email" value="budi@example.com" required
                                   class="w-full text-sm px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-xs text-slate-500 block">Item Terpilih:</span>
                            <span id="selected-product-name" class="font-bold text-sm text-slate-800 block">E-Book Panduan Mahir Laravel 11 & ESC-POS</span>
                            <span id="selected-product-price" class="text-indigo-600 font-extrabold text-base block mt-1">Rp 50.000</span>
                            <input type="hidden" id="selected-product-id" value="DIG-01">
                            <input type="hidden" id="selected-price" value="50000">
                        </div>

                        <!-- Tombol Pay Button Sesuai Halaman 20 BKPM -->
                        <button type="button" id="pay-button" 
                                class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base shadow hover:shadow-md transition flex items-center justify-center gap-2">
                            <span>Bayar Sekarang</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>

                    <div id="status-box" class="hidden mt-4 p-3 rounded-lg text-xs"></div>
                </div>

                <!-- Riwayat Pesanan Terbaru -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="font-bold text-sm text-slate-900 mb-3 flex items-center gap-2">
                        <span>📜</span> Riwayat Transaksi Orders
                    </h3>
                    <div id="orders-list" class="space-y-2 text-xs">
                        @forelse($recentOrders as $order)
                            <div class="p-2.5 bg-slate-50 rounded-lg border flex justify-between items-center">
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $order->order_id }}</div>
                                    <div class="text-slate-500">Rp {{ number_format($order->gross_amount, 0, ',', '.') }}</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $order->status === 'PAID' ? 'bg-emerald-100 text-emerald-700' : ($order->status === 'FAILED' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-slate-400 italic">Belum ada order dicatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript Integrasi Midtrans Sesuai Acara 38 Halaman 20 - 21 -->
    <script>
        function selectProduct(id, name, price) {
            document.getElementById('selected-product-id').value = id;
            document.getElementById('selected-product-name').innerText = name;
            document.getElementById('selected-product-price').innerText = 'Rp ' + Number(price).toLocaleString('id-ID');
            document.getElementById('selected-price').value = price;
        }

        document.getElementById('pay-button').addEventListener('click', function () {
            const btn = document.getElementById('pay-button');
            const statusBox = document.getElementById('status-box');
            
            const productId = document.getElementById('selected-product-id').value;
            const productName = document.getElementById('selected-product-name').innerText;
            const price = document.getElementById('selected-price').value;
            const customerName = document.getElementById('customer-name').value;
            const customerEmail = document.getElementById('customer-email').value;

            btn.disabled = true;
            btn.innerHTML = 'Memproses Token...';
            statusBox.className = 'mt-4 p-3 rounded-lg text-xs bg-blue-50 text-blue-700 block';
            statusBox.innerText = 'Mengirim request ke backend Laravel (/checkout)...';

            fetch('/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    product_name: productName,
                    price: price,
                    customer_name: customerName,
                    customer_email: customerEmail
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = 'Bayar Sekarang';

                if (data.snap_token) {
                    statusBox.className = 'mt-4 p-3 rounded-lg text-xs bg-indigo-50 text-indigo-700 block';
                    statusBox.innerText = 'Order ID: ' + data.order_id + ' | Membuka popup pembayaran Midtrans...';

                    // Cek jika menggunakan mock/dummy token
                    if (data.is_simulated || typeof window.snap === 'undefined' || !window.snap.pay) {
                        const mockConfirm = confirm("Mode Simulasi Sandbox: Apakah Anda ingin mensimulasikan pembayaran SUKSES untuk order: " + data.order_id + "?");
                        if (mockConfirm) {
                            simulateNotification(data.order_id, 'settlement');
                        }
                        return;
                    }

                    // Pemanggilan resmi Midtrans Snap JS (Acara 38 Hal 20)
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            alert("Pembayaran sukses!");
                            statusBox.className = 'mt-4 p-3 rounded-lg text-xs bg-emerald-50 text-emerald-700 block';
                            statusBox.innerText = 'Pembayaran berhasil! Order ID: ' + data.order_id;
                            setTimeout(() => window.location.reload(), 1500);
                        },
                        onPending: function(result) {
                            alert("Menunggu pembayaran...");
                            statusBox.className = 'mt-4 p-3 rounded-lg text-xs bg-amber-50 text-amber-700 block';
                            statusBox.innerText = 'Menunggu pembayaran diselesaikan oleh pengguna...';
                        },
                        onError: function(result) {
                            alert("Pembayaran gagal!");
                            statusBox.className = 'mt-4 p-3 rounded-lg text-xs bg-rose-50 text-rose-700 block';
                            statusBox.innerText = 'Pembayaran gagal diproses.';
                        },
                        onClose: function() {
                            statusBox.innerText = 'Popup pembayaran ditutup oleh pengguna.';
                        }
                    });
                } else {
                    statusBox.className = 'mt-4 p-3 rounded-lg text-xs bg-rose-50 text-rose-700 block';
                    statusBox.innerText = 'Error: ' + (data.error || 'Gagal membuat snap token');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Bayar Sekarang';
                statusBox.className = 'mt-4 p-3 rounded-lg text-xs bg-rose-50 text-rose-700 block';
                statusBox.innerText = 'Network Error: ' + err.message;
            });
        });

        // Helper untuk simulasi webhook saat pengujian lokal
        function simulateNotification(orderId, status) {
            fetch('/midtrans/notification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId,
                    transaction_status: status,
                    payment_type: 'bank_transfer',
                    fraud_status: 'accept'
                })
            })
            .then(res => res.json())
            .then(res => {
                alert("Simulasi notifikasi webhook Midtrans berhasil diproses: Status " + res.status);
                window.location.reload();
            });
        }
    </script>
</body>
</html>

