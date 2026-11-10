<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            try {
                if (localStorage.getItem('forum-theme') === 'dark') {
                    document.documentElement.dataset.theme = 'dark';
                }
            } catch (error) {
                console.error('Không thể tải giao diện đã lưu.', error);
            }
        </script>

        <title>{{ config('app.name', 'Hỏi đáp Khoa') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="site-background font-sans antialiased text-gray-900 flex flex-col min-h-screen">
        <div class="site-content flex-1">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header @class([
                    'border-b shadow-sm',
                    'bg-blue-600 border-blue-700 text-white' => request()->routeIs('questions.index', 'stats', 'books.index'),
                    'bg-white border-gray-100' => !request()->routeIs('questions.index', 'stats', 'books.index'),
                ])>
                    <div class="max-w-7xl mx-auto min-h-[82px] flex items-center px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Chân trang đơn giản -->
        <footer class="site-content bg-white border-t border-gray-200 mt-12 py-6 text-center text-sm text-gray-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-blue-600">{{ config('app.name', 'Hỏi đáp Khoa') }}</span>
                    <span>&copy; {{ date('Y') }}</span>
                </div>
                <div class="text-xs text-gray-400">
                    Đồ án niên luận
                </div>
            </div>
        </footer>
    </body>
</html>
