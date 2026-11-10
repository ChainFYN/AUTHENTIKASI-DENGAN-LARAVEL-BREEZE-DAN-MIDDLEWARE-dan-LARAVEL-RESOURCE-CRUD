<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; border-radius: 8px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e74c3c; padding-bottom: 20px; }
        .header h1 { color: #e74c3c; margin: 0 0 10px 0; }
        .content { line-height: 1.6; color: #555; }
        .alert { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $title }}</h1>
            <p>Notifikasi dari Minimarket System</p>
        </div>

        <div class="content">
            <p>Yth. Pengguna,</p>
            
            <div class="alert">
                {{ $message }}
            </div>

            @if (!empty($data))
                <p><strong>Detail:</strong></p>
                <ul>
                    @foreach ($data as $key => $value)
                        <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                    @endforeach
                </ul>
            @endif

            <p>Terima kasih telah menggunakan sistem kami.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Minimarket Digital Management System. All rights reserved.</p>
            <p>Email ini dikirim otomatis, mohon tidak membalas.</p>
        </div>
    </div>
</body>
</html>
