<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-bold text-gray-900">Đăng sách cũ</h2></x-slot>
    <div class="max-w-2xl mx-auto py-8 px-4">
        <form method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-5">
            @csrf
            <div><x-input-label for="title" value="Tên sách *" /><x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title')" required /><x-input-error :messages="$errors->get('title')" class="mt-1" /></div>
            <div><x-input-label for="author" value="Tác giả" /><x-text-input id="author" name="author" class="mt-1 block w-full" :value="old('author')" /></div>
            <div><x-input-label for="description" value="Mô tả" /><textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md">{{ old('description') }}</textarea></div>
            <div>
                <x-input-label for="image" value="Ảnh sách (không bắt buộc)" />
                <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp" class="mt-1 block w-full text-sm text-gray-600">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG, GIF hoặc WEBP; tối đa 5MB.</p>
                <x-input-error :messages="$errors->get('image')" class="mt-1" />
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><x-input-label for="type" value="Hình thức *" /><select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300"><option value="sale">Bán</option><option value="giveaway" @selected(old('type') === 'giveaway')>Cho tặng</option></select></div>
                <div><x-input-label for="price" value="Giá (đ)" /><x-text-input id="price" name="price" type="number" min="0" class="mt-1 block w-full" :value="old('price')" /></div>
                <div><x-input-label for="condition" value="Tình trạng *" /><select id="condition" name="condition" class="mt-1 block w-full rounded-md border-gray-300"><option value="new">Mới</option><option value="good">Tốt</option><option value="used" @selected(old('condition', 'used') === 'used')>Đã qua sử dụng</option></select></div>
                <div><x-input-label for="contact" value="Thông tin liên hệ" /><x-text-input id="contact" name="contact" class="mt-1 block w-full" :value="old('contact')" placeholder="Email, Zalo..." /></div>
            </div>
            <div class="flex justify-end gap-3"><a href="{{ route('books.index') }}" class="px-4 py-2 text-sm text-gray-600">Hủy</a><x-primary-button>Đăng sách</x-primary-button></div>
        </form>
    </div>
</x-app-layout>
