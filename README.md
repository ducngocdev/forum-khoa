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
- Đăng câu hỏi, trả lời, bình luận theo môn học
- Bình chọn, chọn câu trả lời được chấp nhận
- Tìm kiếm full-text theo từ khóa, lọc theo môn và thẻ, sắp xếp theo mới nhất/nhiều phiếu/nhiều lượt xem
- Gợi ý câu hỏi tương tự khi đang soạn câu hỏi mới
- Chống gian lận bình chọn (không tự bầu, giới hạn tần suất, phát hiện bầu dồn, phiếu nghi vấn không tính điểm)
- Điểm uy tín thành viên và lịch sử điểm
- Báo cáo vi phạm, kiểm duyệt nội dung
- Thống kê câu hỏi theo môn, người đóng góp nổi bật (dùng SQL view)
- Hồ sơ cá nhân có ảnh đại diện, vai trò/học vị, lớp, năm học, giới thiệu và lịch sử hoạt động
- Chợ sách cũ để thành viên đăng bán hoặc cho tặng, tìm kiếm và lọc theo hình thức; ảnh sách tùy chọn (JPG, PNG, GIF, WEBP, tối đa 5MB)

## Cài đặt
1. Bật MySQL, tạo database `forum_khoa` (utf8mb4_unicode_ci)
2. `composer install` và `npm install`
3. `copy .env.example .env` rồi `php artisan key:generate`
4. `php artisan migrate --seed` và `php artisan storage:link`
5. `npm run build`, sau đó `php artisan serve` và mở http://127.0.0.1:8000

## Tài khoản thử (mật khẩu: password)
- Quản trị: admin@forum.test
- Kiểm duyệt viên: mod@forum.test
- Sinh viên: a@forum.test, b@forum.test
