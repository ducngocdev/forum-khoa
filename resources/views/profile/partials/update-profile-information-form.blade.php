<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @if($errors->any())
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Không thể lưu hồ sơ:</p>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
            @endif

            <div x-data="{ initialPreview: @js($user->avatar_path ? asset('storage/' . $user->avatar_path) : ''), preview: @js($user->avatar_path ? asset('storage/' . $user->avatar_path) : ''), failed: false }">
                    <x-input-label for="avatar" value="Ảnh đại diện" />
                    <div class="mt-2 flex items-center gap-4">
                        <template x-if="preview && !failed">
                            <img :src="preview" alt="Ảnh đại diện xem trước" class="w-16 h-16 rounded-full object-cover border border-gray-200" x-on:error="failed = true">
                        </template>
                        <template x-if="!preview || failed">
                            <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-xl font-bold">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                        </template>
                        <div>
                            <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" x-ref="avatar" class="block w-full text-sm text-gray-600"
                                @change="const file = $event.target.files[0]; if (file) { if (preview.startsWith('blob:')) URL.revokeObjectURL(preview); preview = URL.createObjectURL(file); failed = false; const remove = document.getElementById('remove_avatar'); if (remove) remove.checked = false }">
                            <p class="mt-1 text-xs text-gray-500">Ảnh sẽ hiện xem trước ngay sau khi chọn.</p>
                            @if($user->avatar_path)
                                <label for="remove_avatar" class="mt-2 inline-flex items-center gap-2 text-sm text-red-600">
                                    <input id="remove_avatar" name="remove_avatar" type="checkbox" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500"
                                        x-on:change="if ($event.target.checked) { preview = ''; failed = false } else if (!$refs.avatar.files.length) { preview = initialPreview; failed = false }">
                                    Gỡ ảnh đại diện hiện tại
                                </label>
                            @endif
                        </div>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>

            <div>
                    <x-input-label for="academic_title" value="Vai trò / học vị" />
                    <select id="academic_title" name="academic_title" onchange="document.getElementById('class_name').required = this.value === 'Sinh viên'" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        @foreach(['' => 'Chọn vai trò / học vị', 'Sinh viên' => 'Sinh viên', 'Giáo viên' => 'Giáo viên', 'Thạc sĩ' => 'Thạc sĩ', 'Tiến sĩ' => 'Tiến sĩ', 'Khác' => 'Khác'] as $value => $label)
                            <option value="{{ $value }}" @selected(strtolower((string) old('academic_title', $user->academic_title)) === strtolower($value))>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('academic_title')" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ role: '{{ old('academic_title', $user->academic_title) }}' }" x-init="$watch('role', value => document.getElementById('academic_title').value = value)">
                    <div>
                        <x-input-label for="class_name" value="Lớp / đơn vị (chỉ bắt buộc với sinh viên)" />
                        <x-text-input id="class_name" name="class_name" type="text" class="mt-1 block w-full" :value="old('class_name', $user->class_name)" :required="old('academic_title', $user->academic_title) === 'Sinh viên'" placeholder="Ví dụ: DI22A1" />
                        <x-input-error class="mt-2" :messages="$errors->get('class_name')" />
                    </div>
                    <div>
                        <x-input-label for="study_year" value="Kinh nghiệm" />
                        <select id="study_year" name="study_year" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Chọn kinh nghiệm</option>
                            @foreach(['Năm 1', 'Năm 2', 'Năm 3', 'Năm 4', 'Năm 5', 'Năm 6', 'Năm 9 trở lên', 'Đã tốt nghiệp'] as $experience)
                                <option value="{{ $experience }}" @selected(old('study_year', $user->study_year) === $experience)>{{ $experience }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('study_year')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="bio" value="Giới thiệu bản thân" />
                    <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Chia sẻ thêm về bạn...">{{ old('bio', $user->bio) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Lưu và xem trang cá nhân</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
