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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>