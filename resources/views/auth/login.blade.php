<x-guest-layout>
    <div class="auth-form">
        <div class="auth-tabs" role="tablist" aria-label="Tài khoản">
            <a class="auth-tabs__link auth-tabs__link--active" href="{{ route('login') }}" aria-current="page">Đăng nhập</a>
            <a class="auth-tabs__link" href="{{ route('register') }}">Tạo tài khoản</a>
        </div>

        <div class="auth-heading">
            <p class="auth-eyebrow">CHÀO MỪNG TRỞ LẠI</p>
            <h2>Đăng nhập</h2>
            <p>Rất vui được gặp lại bạn. Hãy tiếp tục hành trình học hỏi nhé!</p>
        </div>

        <x-auth-session-status class="auth-status" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="auth-fields">
            @csrf

            <div class="auth-field">
                <label for="email">Địa chỉ email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="ban@email.com" autocomplete="username" required autofocus data-auth-email>
                <x-input-error :messages="$errors->get('email')" class="auth-error" />
            </div>

            <div class="auth-field">
                <div class="auth-field__topline">
                    <label for="password">Mật khẩu</label>
                    @if (Route::has('password.request'))
                        <a class="auth-subtle-link" href="{{ route('password.request') }}">Quên mật khẩu?</a>
                    @endif
                </div>
                <div class="auth-password-control">
                    <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" autocomplete="current-password" required data-auth-password>
                    <button class="auth-password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu" aria-pressed="false">Hiện</button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="auth-error" />
            </div>

            <label class="auth-remember" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Ghi nhớ đăng nhập</span>
            </label>

            <button class="auth-submit" type="submit">
                Đăng nhập
                <span aria-hidden="true">→</span>
            </button>
        </form>

        <p class="auth-switch">Bạn chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
    </div>
</x-guest-layout>
