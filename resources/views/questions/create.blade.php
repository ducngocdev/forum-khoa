<x-app-layout>
  <x-slot name="header">
    <div class="mx-auto flex max-w-5xl items-center justify-between gap-4">
      <div>
        <p class="text-xs font-semibold uppercase tracking-[.18em] text-blue-600">Cộng đồng học thuật</p>
        <h2 class="mt-1 text-xl font-bold text-gray-900">Đăng bài</h2>
      </div>
      <a href="{{ route('questions.index') }}" class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-600 transition hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900">
        Hủy và quay lại
      </a>
    </div>
  </x-slot>

  <div class="question-editor min-h-[calc(100vh-12rem)] px-4 py-8 sm:px-6">
    <div class="mx-auto max-w-5xl">
      <div class="mx-auto max-w-3xl">
        @if($errors->any())
          <div class="mb-5 rounded-2xl border border-red-200 bg-red-50/95 p-4 text-sm text-red-700 shadow-sm" role="alert">
            <p class="mb-1 font-bold">Chưa thể đăng bài</p>
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('questions.store') }}" enctype="multipart/form-data" class="question-composer space-y-5">
          @csrf
          <div class="question-composer__intro">
            <span class="question-composer__icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Bạn muốn đăng bài gì?</h3>
              <p class="mt-1 text-sm text-gray-600">Chọn loại bài viết phù hợp để mọi người dễ theo dõi và trao đổi.</p>
            </div>
          </div>

          <fieldset>
            <legend class="question-field-label">Loại bài viết</legend>
            <div class="grid gap-3 sm:grid-cols-2">
              <label class="post-type-option">
                <input type="radio" name="post_type" value="question" @checked(old('post_type', 'question') === 'question') required>
                <span class="post-type-option__content">
                  <span class="post-type-option__title">Thắc mắc</span>
                  <span class="post-type-option__description">Đặt câu hỏi và nhận câu trả lời từ cộng đồng.</span>
                </span>
              </label>
              <label class="post-type-option">
                <input type="radio" name="post_type" value="share" @checked(old('post_type') === 'share') required>
                <span class="post-type-option__content">
                  <span class="post-type-option__title">Chia sẻ</span>
                  <span class="post-type-option__description">Chia sẻ kiến thức, kinh nghiệm hoặc tài liệu.</span>
                </span>
              </label>
            </div>
            <x-input-error :messages="$errors->get('post_type')" class="mt-2" />
          </fieldset>

          <div>
            <label for="question-title" class="question-field-label">Tiêu đề bài viết</label>
            <input id="question-title" name="title" value="{{ old('title') }}" required minlength="10" maxlength="200" placeholder="Ví dụ: Cách khắc phục lỗi kết nối MySQL trong Laravel?" class="question-field question-field--title">
            <div id="similar" class="mt-2 space-y-1 text-sm"></div>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="question-subject" class="question-field-label">Môn học <span class="font-normal text-gray-400">(không bắt buộc)</span></label>
              <select id="question-subject" name="subject_id" class="question-field">
                <option value="">Chưa chọn môn học</option>
                @foreach($subjects as $subject)
                  <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->code }} · {{ $subject->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label for="question-tags" class="question-field-label">Thẻ chủ đề</label>
              <input id="question-tags" name="tags" value="{{ old('tags') }}" placeholder="php, sql, thuật toán" class="question-field">
              <p class="mt-1.5 text-xs text-gray-500">Các thẻ cách nhau bằng dấu phẩy, tối đa 5 thẻ.</p>
            </div>
          </div>

          <div>
            <label for="question-body" class="question-field-label">Nội dung chi tiết</label>
            <textarea id="question-body" name="body" rows="9" required minlength="20" placeholder="Mô tả chi tiết nội dung, bối cảnh hoặc điều bạn muốn mọi người cùng trao đổi. (hỗ trợ Markdown và khối mã ```)" class="question-field question-field--body">{{ old('body') }}</textarea>
            <p class="mt-1.5 text-xs text-gray-500">Tối thiểu 20 ký tự. Không chia sẻ thông tin cá nhân nhạy cảm.</p>
          </div>

          <div>
            <div class="mb-2 flex items-center justify-between gap-3">
              <div>
                <label for="question-attachments" class="question-field-label mb-0">Ảnh hoặc video</label>
                <p class="mt-1 text-xs text-gray-500">Tối đa 6 tệp · ảnh 5 MB/tệp · video 50 MB/tệp</p>
              </div>
              <label for="question-attachments" class="question-media-picker">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.7"/><circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/><path d="m21 15-5-5L5 21" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Chọn tệp
              </label>
            </div>
            <input id="question-attachments" name="attachments[]" type="file" multiple accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/webm,video/quicktime" class="sr-only">
            <div data-attachment-preview class="question-attachment-preview" aria-live="polite"></div>
          </div>

          <div class="flex flex-col-reverse items-stretch justify-between gap-3 border-t border-gray-200/80 pt-5 sm:flex-row sm:items-center">
            <a href="{{ route('questions.index') }}" class="text-center text-sm font-semibold text-gray-500 transition hover:text-gray-800 sm:text-left">Hủy, không đăng bài</a>
            <button type="submit" class="question-submit">
              <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4 20-7ZM22 2 11 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Đăng bài
            </button>
          </div>
        </form>

        <div class="question-composer__hint mt-4">
          <span aria-hidden="true">✦</span>
          <p>Tiêu đề cụ thể và nội dung có bối cảnh sẽ giúp bài viết hữu ích hơn với cộng đồng.</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    (() => {
      const title = document.getElementById('question-title');
      const similar = document.getElementById('similar');
      let timer;
      let request;

      title.addEventListener('input', () => {
        clearTimeout(timer);
        request?.abort();
        timer = setTimeout(async () => {
          request = new AbortController();
          try {
            const response = await fetch('{{ route('questions.similar') }}?title=' + encodeURIComponent(title.value), {
              headers: {Accept: 'application/json'},
              signal: request.signal
            });
            if (!response.ok) return;
            const items = await response.json();
            similar.replaceChildren();
            if (!items.length) return;
            const heading = document.createElement('p');
            heading.className = 'font-semibold text-gray-700';
            heading.textContent = 'Bài viết tương tự, có thể đã có lời giải:';
            similar.appendChild(heading);
            items.forEach((item) => {
              const link = document.createElement('a');
              link.href = item.url;
              link.target = '_blank';
              link.rel = 'noopener noreferrer';
              link.textContent = item.title;
              link.className = 'block text-blue-700 underline';
              similar.appendChild(link);
            });
          } catch (error) {
            if (error.name !== 'AbortError') console.error('Không thể tải gợi ý câu hỏi.', error);
          }
        }, 400);
      });

      const attachmentInput = document.getElementById('question-attachments');
      const preview = document.querySelector('[data-attachment-preview]');
      attachmentInput.addEventListener('change', () => {
        preview.replaceChildren();
        const files = Array.from(attachmentInput.files || []);
        if (files.length > 6) {
          attachmentInput.setCustomValidity('Bạn chỉ có thể đính kèm tối đa 6 tệp.');
          attachmentInput.reportValidity();
          attachmentInput.value = '';
          return;
        }
        attachmentInput.setCustomValidity('');
        files.forEach((file) => {
          const item = document.createElement('div');
          item.className = 'question-attachment-preview__item';
          if (file.type.startsWith('image/')) {
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = '';
            image.onload = () => URL.revokeObjectURL(image.src);
            item.appendChild(image);
          } else {
            const video = document.createElement('span');
            video.className = 'question-attachment-preview__video';
            video.textContent = '▶ Video';
            item.appendChild(video);
          }
          const name = document.createElement('span');
          name.textContent = file.name;
          name.title = file.name;
          item.appendChild(name);
          preview.appendChild(item);
        });
      });
    })();
  </script>
</x-app-layout>
