<x-app-layout>
    <x-slot name="header">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.18em] text-blue-600">Chợ sách trong Khoa</p>
                <h2 class="mt-1 text-xl font-bold text-gray-900">Đăng sách cũ</h2>
            </div>
            <a href="{{ route('books.index') }}" class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                Hủy và quay lại
            </a>
        </div>
    </x-slot>

    <div class="question-editor min-h-[calc(100vh-12rem)] px-4 py-8 sm:px-6">
        <div class="mx-auto max-w-5xl">
            <div class="mx-auto max-w-3xl">
                @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50/95 p-4 text-sm text-red-700 shadow-sm" role="alert">
                        <p class="mb-1 font-bold">Chưa thể đăng sách</p>
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="question-composer space-y-5">
                    @csrf
                    <div class="question-composer__intro">
                        <span class="question-composer__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16ZM4 18.5A2.5 2.5 0 0 1 6.5 16H20M8 7h8m-8 4h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Chia sẻ sách với mọi người</h3>
                            <p class="mt-1 text-sm text-gray-600">Điền thông tin để bán hoặc tặng lại sách cho thành viên trong Khoa.</p>
                        </div>
                    </div>

                    <div>
                        <label for="title" class="question-field-label">Tên sách <span class="text-blue-600">*</span></label>
                        <input id="title" name="title" value="{{ old('title') }}" required maxlength="200" class="question-field" placeholder="Nhập tên sách">
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="author" class="question-field-label">Tác giả</label>
                            <input id="author" name="author" value="{{ old('author') }}" class="question-field" placeholder="Tên tác giả">
                            <x-input-error :messages="$errors->get('author')" class="mt-1" />
                        </div>
                        <div>
                            <label for="type" class="question-field-label">Hình thức <span class="text-blue-600">*</span></label>
                            <select id="type" name="type" required class="question-field">
                                <option value="sale" @selected(old('type', 'sale') === 'sale')>Bán</option>
                                <option value="giveaway" @selected(old('type') === 'giveaway')>Cho tặng</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-1" />
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="price" class="question-field-label">Giá (đ)</label>
                            <input id="price" name="price" type="number" min="0" value="{{ old('price') }}" class="question-field" placeholder="Ví dụ: 50000">
                            <x-input-error :messages="$errors->get('price')" class="mt-1" />
                        </div>
                        <div>
                            <label for="condition" class="question-field-label">Tình trạng <span class="text-blue-600">*</span></label>
                            <select id="condition" name="condition" required class="question-field">
                                <option value="new" @selected(old('condition') === 'new')>Mới</option>
                                <option value="good" @selected(old('condition') === 'good')>Tốt</option>
                                <option value="used" @selected(old('condition', 'used') === 'used')>Đã qua sử dụng</option>
                            </select>
                            <x-input-error :messages="$errors->get('condition')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <label for="description" class="question-field-label">Mô tả</label>
                        <textarea id="description" name="description" rows="5" class="question-field question-field--body" placeholder="Mô tả nội dung, tình trạng hoặc thông tin hữu ích khác">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="image" class="question-field-label">Ảnh sách <span class="font-normal text-gray-400">(không bắt buộc)</span></label>
                            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" class="question-field h-auto py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-semibold file:text-blue-700">
                            <p class="mt-1.5 text-xs text-gray-500">JPG, PNG, GIF hoặc WEBP; tối đa 5MB.</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-1" />
                        </div>
                        <div>
                            <label for="contact" class="question-field-label">Thông tin liên hệ</label>
                            <input id="contact" name="contact" value="{{ old('contact') }}" class="question-field" placeholder="Email, Zalo...">
                            <x-input-error :messages="$errors->get('contact')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse items-stretch justify-between gap-3 border-t border-gray-200/80 pt-5 sm:flex-row sm:items-center">
                        <a href="{{ route('books.index') }}" class="text-center text-sm font-semibold text-gray-500 transition hover:text-blue-700 sm:text-left">Hủy, không đăng sách</a>
                        <button type="submit" class="question-submit">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Đăng sách
                        </button>
                    </div>
                </form>

                <div class="question-composer__hint mt-4">
                    <span aria-hidden="true">✦</span>
                    <p>Hãy mô tả đúng tình trạng sách và kiểm tra thông tin liên hệ trước khi đăng.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
