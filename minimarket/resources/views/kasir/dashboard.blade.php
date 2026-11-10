<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Kasir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-2 text-emerald-700">
                        Dashboard Kasir
                    </h1>
                    <p class="text-gray-600 mb-4">
                        Selamat datang, <span class="font-semibold text-gray-800">{{ auth()->user()->name }}</span> (Role: <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold text-xs uppercase">{{ auth()->user()->role }}</span>)
                    </p>
                    <div class="mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                        <p class="text-sm text-emerald-800">
                            Halaman ini dilindungi oleh <strong>RoleMiddleware</strong> (<code>role:kasir</code>). Hanya user dengan peran kasir yang dapat mengakses halaman ini.
                        </p>
                    </div>

                    <!-- Acara 35 - 38 Quick Links -->
                    <div class="mt-6 border-t pt-6">
                        <h3 class="font-bold text-gray-800 text-sm mb-3">Materi Praktikum Minggu 11: Print ESC/POS & Midtrans Gateway</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <a href="{{ route('print.receipt') }}" target="_blank" class="p-3 border rounded-lg bg-slate-50 hover:bg-slate-100 transition flex flex-col justify-between">
                                <span class="font-bold text-xs text-indigo-700">Acara 35: Print Dasar</span>
                                <span class="text-xs text-gray-600 mt-1">Uji cetak struk ESC/POS Mike42</span>
                            </a>
                            <a href="{{ route('kopikita.print') }}" target="_blank" class="p-3 border rounded-lg bg-amber-50 hover:bg-amber-100 transition flex flex-col justify-between">
                                <span class="font-bold text-xs text-amber-800">Acara 36: Toko Kopi Kita</span>
                                <span class="text-xs text-gray-600 mt-1">Studi kasus struk otomatis Kopi Kita</span>
                            </a>
                            <a href="{{ route('digital.products') }}" target="_blank" class="p-3 border rounded-lg bg-indigo-50 hover:bg-indigo-100 transition flex flex-col justify-between">
                                <span class="font-bold text-xs text-indigo-800">Acara 37 & 38: Midtrans</span>
                                <span class="text-xs text-gray-600 mt-1">Checkout Snap & Webhook Order</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>