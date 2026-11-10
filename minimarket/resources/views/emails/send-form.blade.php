@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Kirim Email</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Email Selamat Datang</h2>
                <form method="POST" action="{{ route('email.send.welcome') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
                        <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email Penerima</label>
                        <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Kirim Email Selamat Datang
                    </button>
                </form>
                <div class="mt-4">
                    <a href="{{ route('email.preview.welcome') }}" class="text-blue-500 hover:underline text-sm">
                        Preview Email
                    </a>
                </div>
            </div>

            <div class="bg-white rounded shadow-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Email Notifikasi</h2>
                <form method="POST" action="{{ route('email.send.notification') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Judul Email</label>
                        <input type="text" name="title" value="Pemberitahuan Penting" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email Penerima</label>
                        <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Isi Pesan</label>
                        <textarea name="message" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded" required>Pesanan baru telah diproses.</textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Kirim Email Notifikasi
                    </button>
                </form>
                <div class="mt-4">
                    <a href="{{ route('email.preview.notification') }}" class="text-green-500 hover:underline text-sm">
                        Preview Email
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded shadow-lg p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Informasi Konfigurasi Email</h2>
            <p class="text-gray-700 mb-2">Untuk mengirim email secara aktual, pastikan Anda telah mengkonfigurasi file <code>.env</code> dengan data SMTP yang valid:</p>
            <pre class="bg-gray-100 p-3 rounded text-sm">
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=admin@example.com
MAIL_FROM_NAME="${APP_NAME}"
            </pre>
            <p class="text-gray-700 mt-2">Atau gunakan driver <code>log</code> untuk testing lokal (email akan dicatat di log, tidak dikirim).</p>
        </div>
    </div>
</div>
@endsection
