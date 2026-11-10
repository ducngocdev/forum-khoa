<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

# Diễn đàn hỏi đáp học thuật cho sinh viên khoa

Niên luận: hệ thống hỏi đáp riêng của khoa, thay cho việc hỏi rải rác trong nhóm chat.
Công nghệ: Laravel 12, MySQL (full-text search), Blade, Tailwind CSS.

## Chức năng
- Đăng bài theo hai dạng Thắc mắc và Chia sẻ; trả lời, bình luận theo môn học
- Bình chọn, chọn câu trả lời được chấp nhận
- Tìm kiếm full-text theo từ khóa, lọc theo môn và thẻ, sắp xếp theo mới nhất/nhiều phiếu/nhiều lượt xem
- Gợi ý câu hỏi tương tự khi đang soạn câu hỏi mới
- Đăng câu hỏi không bắt buộc chọn môn học, đính kèm tối đa 6 tệp (ảnh tối đa 5 MB/tệp, video tối đa 50 MB/tệp)
- Chống gian lận bình chọn (không tự bầu, giới hạn tần suất, phát hiện bầu dồn, phiếu nghi vấn không tính điểm)
- Điểm uy tín thành viên và lịch sử điểm
- Báo cáo vi phạm, kiểm duyệt nội dung
- Thống kê câu hỏi theo môn, người đóng góp nổi bật (dùng SQL view)
- Hồ sơ cá nhân có ảnh đại diện, vai trò/học vị, lớp, năm học, giới thiệu và lịch sử hoạt động
- Chợ sách cũ để thành viên đăng bán hoặc cho tặng, tìm kiếm và lọc theo hình thức; ảnh sách tùy chọn (JPG, PNG, GIF, WEBP, tối đa 5MB)

## Quy tắc dữ liệu
- Câu trả lời được chấp nhận chỉ được tham chiếu tại `questions.accepted_answer_id`; khóa ngoại ghép bảo đảm câu trả lời thuộc đúng câu hỏi.
- Điểm uy tín lưu và tính toán theo giá trị có dấu để lịch sử cộng/trừ luôn khớp; giao diện chỉ hiển thị tối thiểu là 0.
- Lịch sử uy tín phân biệt bình chọn mới, hủy bình chọn (`vote_cancelled`) và đổi chiều (`vote_changed`).
- Báo cáo lưu người gửi và người kiểm duyệt đã xử lý bằng quan hệ khóa ngoại; khi xóa người kiểm duyệt, báo cáo vẫn được giữ lại.

## Cài đặt
1. Bật MySQL, tạo database `forum_khoa` (utf8mb4_unicode_ci)
2. `composer install` và `npm install`
3. `copy .env.example .env` rồi `php artisan key:generate`
4. Bật tiện ích PHP `fileinfo` trong `php.ini` để kiểm tra loại tệp tải lên. Trên Windows, có thể dùng `run.bat` để chạy server cục bộ kèm tiện ích này.
5. `php artisan migrate --seed` và `php artisan storage:link`
6. `npm run build`, sau đó chạy `php artisan serve` và mở http://127.0.0.1:8000; hoặc dùng `run.bat` trên Windows.

## Tài khoản thử (mật khẩu: password)
- Quản trị: admin@forum.test
- Kiểm duyệt viên: mod@forum.test
- Sinh viên: a@forum.test, b@forum.test
