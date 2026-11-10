<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Selamat Datang di Minimarket!</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; border-radius: 8px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3498db; padding-bottom: 20px; }
        .header h1 { color: #3498db; margin: 0 0 10px 0; }
        .content { line-height: 1.6; color: #555; }
        .button { display: inline-block; background-color: #3498db; color: white; text-decoration: none; padding: 12px 30px; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Selamat Datang, {{ $user->name }}!</h1>
            <p>Minimarket Digital Management System</p>
        </div>

        <div class="content">
            <p>Terima kasih telah mendaftar di sistem Minimarket kami. Akun Anda telah berhasil dibuat.</p>
            
            <p>Dengan akun ini, Anda dapat:</p>
            <ul>
                <li>Mengelola data produk</li>
                <li>Memproses transaksi penjualan</li>
                <li>Melacak stok barang</li>
                <li>Melihat laporan penjualan</li>
            </ul>

            <p>Silakan login ke sistem menggunakan email Anda:</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>

            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Login ke Dashboard</a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Minimarket Digital Management System. All rights reserved.</p>
            <p>Email ini dikirim otomatis, mohon tidak membalas.</p>
        </div>
    </div>
</body>
</html>
