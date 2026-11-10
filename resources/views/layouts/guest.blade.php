<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Hỏi đáp Khoa') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <main class="auth-page">
            <a class="auth-brand" href="{{ route('home') }}" aria-label="Về trang chủ">
                <span class="auth-brand__mark">H</span>
                <span>{{ config('app.name', 'Hỏi đáp Khoa') }}</span>
            </a>

            <div class="auth-layout">
                <section class="auth-intro" aria-label="Chào mừng">
                    <div class="auth-orbit auth-orbit--one"></div>
                    <div class="auth-orbit auth-orbit--two"></div>
                    <div class="auth-yeti" data-auth-yeti aria-hidden="true">
                        <svg viewBox="0 0 240 240" role="presentation" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="120" cy="120" r="112" fill="#d9b8f4"/>
                            <path d="M37 216c5-43 38-70 83-70s78 27 83 70" fill="#fff" stroke="#57376f" stroke-width="4" stroke-linecap="round"/>
                            <path d="M70 92c0-34 22-57 50-57s50 23 50 57v39c0 31-22 53-50 53s-50-22-50-53V92Z" fill="#f3e7fc" stroke="#57376f" stroke-width="4"/>
                            <path d="M71 83c-10-18-8-35-3-47 13 6 22 14 29 24 11-18 29-29 54-34-4 14-9 24-17 33 14-5 28-7 43-5-9 12-18 21-31 28" fill="#fff" stroke="#57376f" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M68 100c-15-9-27 4-24 17 2 9 10 14 20 12m108-29c15-9 27 4 24 17-2 9-10 14-20 12" fill="#f3e7fc" stroke="#57376f" stroke-width="4"/>
                            <ellipse cx="93" cy="112" rx="5" ry="7" fill="#57376f" class="auth-yeti__eye"/>
                            <ellipse cx="147" cy="112" rx="5" ry="7" fill="#57376f" class="auth-yeti__eye"/>
                            <path d="M116 119c-4 0-6 4-4 7l5 7c2 2 5 2 7 0l5-7c2-3 0-7-4-7h-9Z" fill="#755187"/>
                            <path d="M102 143c10 9 26 9 36 0" fill="none" stroke="#57376f" stroke-width="4" stroke-linecap="round" class="auth-yeti__mouth"/>
                            <path d="M42 133c17-8 35-13 51-15l8 24c-18 6-37 12-53 16l-6-25Z" fill="#fff" stroke="#57376f" stroke-width="4" stroke-linejoin="round" class="auth-yeti__arm auth-yeti__arm--left"/>
                            <path d="M198 133c-17-8-35-13-51-15l-8 24c18 6 37 12 53 16l6-25Z" fill="#fff" stroke="#57376f" stroke-width="4" stroke-linejoin="round" class="auth-yeti__arm auth-yeti__arm--right"/>
                            <path d="M81 174c12-8 24-12 39-12s27 4 39 12c-12 14-25 21-39 21s-27-7-39-21Z" fill="#e5d1f6"/>
                        </svg>
                    </div>
                    <div class="auth-intro__copy">
                        <p class="auth-eyebrow">CÙNG NHAU HỌC HỎI</p>
                        <h1>Mỗi câu hỏi,<br>một bước tiến mới.</h1>
                        <p>Tham gia cộng đồng để chia sẻ kiến thức, tìm lời giải và kết nối với mọi người.</p>
                    </div>
                    <div class="auth-decoration auth-decoration--sparkle">✦</div>
                    <div class="auth-decoration auth-decoration--dot"></div>
                </section>

                <section class="auth-card">
                    {{ $slot }}
                </section>
            </div>

            <p class="auth-footer">© {{ date('Y') }} {{ config('app.name', 'Hỏi đáp Khoa') }} · Chia sẻ để cùng phát triển</p>
        </main>
    </body>
</html>
