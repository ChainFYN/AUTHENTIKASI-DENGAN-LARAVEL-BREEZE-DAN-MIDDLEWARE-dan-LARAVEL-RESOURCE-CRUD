<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @auth
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (window.Echo) {
                    window.Echo.private('user.{{ auth()->id() }}')
                        .listen('.order.placed', (e) => {
                            if (window.toastr) {
                                window.toastr.success(
                                    `Order #${e.order_id} telah ditempatkan!<br>Total: Rp ${new Intl.NumberFormat('id-ID').format(e.total)}`,
                                    'Notifikasi Transaksi',
                                    {
                                        "closeButton": true,
                                        "progressBar": true,
                                        "positionClass": "toast-top-right",
                                        "timeOut": 5000,
                                        "allowHtml": true
                                    }
                                );
                            }
                            console.log('Order Placed Event:', e);
                        });
                }
            });
        </script>
        @endauth
    </body>
</html>
