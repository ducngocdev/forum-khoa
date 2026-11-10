<x-guest-layout>
    <div class="auth-form">
        <div class="auth-tabs" role="tablist" aria-label="Tài khoản">
            <a class="auth-tabs__link" href="{{ route('login') }}">Đăng nhập</a>
            <a class="auth-tabs__link auth-tabs__link--active" href="{{ route('register') }}" aria-current="page">Tạo tài khoản</a>
        </div>

        <div class="auth-heading">
            <p class="auth-eyebrow">BẮT ĐẦU HÀNH TRÌNH</p>
            <h2>Tạo tài khoản</h2>
            <p>Đăng ký miễn phí để tham gia cộng đồng hỏi đáp.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="auth-fields">
            @csrf

            <div class="auth-field">
                <label for="name">Tên của bạn</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Nguyễn Văn A" autocomplete="name" required autofocus>
                <x-input-error :messages="$errors->get('name')" class="auth-error" />
            </div>

            <div class="auth-field">
                <label for="email">Địa chỉ email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="ban@email.com" autocomplete="username" required data-auth-email>
                <x-input-error :messages="$errors->get('email')" class="auth-error" />
            </div>

            <div class="auth-field">
                <label for="password">Mật khẩu</label>
                <div class="auth-password-control">
                    <input id="password" name="password" type="password" placeholder="Tối thiểu 8 ký tự" autocomplete="new-password" required data-auth-password>
                    <button class="auth-password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu" aria-pressed="false">Hiện</button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="auth-error" />
            </div>

            <div class="auth-field">
                <label for="password_confirmation">Nhập lại mật khẩu</label>
                <div class="auth-password-control">
                    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu" autocomplete="new-password" required data-auth-password>
                    <button class="auth-password-toggle" type="button" data-password-toggle aria-label="Hiện mật khẩu" aria-pressed="false">Hiện</button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="auth-error" />
            </div>

            <button class="auth-submit" type="submit">
                Tạo tài khoản
                <span aria-hidden="true">→</span>
            </button>
        </form>

        <p class="auth-switch">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
    </div>
</x-guest-layout>
