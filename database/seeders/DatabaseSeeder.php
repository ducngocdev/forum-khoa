<?php
namespace Database\Seeders;

use App\Models\{Answer, Question, Subject, Tag, User};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@forum.test'],
            ['name' => 'Quản trị', 'password' => 'password', 'role' => 'admin', 'reputation' => 100]
        );
        User::firstOrCreate(
            ['email' => 'mod@forum.test'],
            ['name' => 'Kiểm duyệt viên', 'password' => 'password', 'role' => 'moderator', 'reputation' => 50]
        );
        User::firstOrCreate(['email' => 'a@forum.test'], ['name' => 'Sinh viên A', 'password' => 'password']);
        User::firstOrCreate(['email' => 'b@forum.test'], ['name' => 'Sinh viên B', 'password' => 'password']);
        User::factory(8)->create();

        $subjects = collect([
            ['CT101', 'Lập trình căn bản'],
            ['CT172', 'Cơ sở dữ liệu'],
            ['CT173', 'Mạng máy tính'],
            ['CT200', 'Công nghệ Web'],
        ])->map(fn ($subject) => Subject::firstOrCreate(
            ['code' => $subject[0]],
            ['name' => $subject[1]]
        ));
        $this->call(CurriculumSubjectSeeder::class);

        $tags = collect([
            ['php', 'php'],
            ['laravel', 'laravel'],
            ['sql', 'sql'],
            ['java', 'java'],
            ['mạng', 'mang'],
            ['thuật toán', 'thuat-toan'],
        ])->map(fn ($tag) => Tag::firstOrCreate(
            ['slug' => $tag[1]],
            ['name' => $tag[0]]
        ));

        foreach ([
            ['Khác nhau giữa INNER JOIN và LEFT JOIN là gì?', 'Em chưa hiểu rõ khi nào dùng INNER JOIN và khi nào dùng LEFT JOIN, mong anh chị giải thích kèm ví dụ.'],
            ['Chuẩn hóa dữ liệu đến dạng chuẩn 3NF như thế nào?', 'Em có một bảng đơn hàng chứa cả thông tin khách hàng, nên tách ra sao để đạt 3NF?'],
        ] as [$title, $body]) {
            $question = Question::firstOrCreate(
                ['title' => $title],
                ['user_id' => User::where('email', 'a@forum.test')->value('id'), 'subject_id' => $subjects[1]->id, 'body' => $body]
            );
            $question->tags()->sync([$tags->firstWhere('name', 'sql')->id]);
        }

        $samples = [
            ['Cách cài đặt PHP trên Windows?', 'Em cần cài môi trường PHP để thực hành lập trình trên Windows.'],
            ['Laravel middleware hoạt động như thế nào?', 'Middleware kiểm tra request ở những bước nào trước khi tới controller?'],
            ['Khi nào nên dùng Eloquent relationship?', 'Em đang phân vân giữa truy vấn Eloquent và Query Builder cho quan hệ giữa các bảng.'],
            ['Cách viết truy vấn tìm bản ghi trùng trong SQL?', 'Em muốn tìm các email bị trùng trong một bảng người dùng.'],
            ['Phân biệt interface và abstract class trong Java', 'Trong bài tập Java, trường hợp nào nên chọn interface thay vì abstract class?'],
            ['Subnet mask dùng để làm gì?', 'Em muốn hiểu cách subnet mask xác định phần mạng và phần host của địa chỉ IP.'],
            ['Thuật toán tìm kiếm nhị phân có điều kiện gì?', 'Dữ liệu cần được tổ chức ra sao để áp dụng tìm kiếm nhị phân?'],
            ['Làm sao tạo migration trong Laravel?', 'Em muốn thêm một cột mới vào bảng hiện có bằng migration.'],
            ['Cách chống SQL injection khi dùng PHP?', 'Nên truyền tham số cho câu truy vấn như thế nào để an toàn?'],
            ['Sự khác nhau giữa GET và POST?', 'Em cần chọn phương thức HTTP phù hợp cho form gửi dữ liệu.'],
            ['Làm sao phân trang dữ liệu với Laravel?', 'Một trang danh sách có nhiều bản ghi thì nên triển khai pagination ra sao?'],
            ['Khóa ngoại trong cơ sở dữ liệu có tác dụng gì?', 'Em muốn hiểu lợi ích của foreign key và các kiểu xóa liên đới.'],
            ['Độ phức tạp O(n log n) thường gặp ở đâu?', 'Những thuật toán sắp xếp phổ biến nào có độ phức tạp này?'],
            ['Java xử lý ngoại lệ như thế nào?', 'Em nên dùng try-catch và khai báo throws trong tình huống nào?'],
            ['Cách tạo API route trong Laravel 12?', 'Dự án Blade hiện tại muốn bổ sung một endpoint JSON thì bắt đầu từ đâu?'],
            ['TCP khác UDP ở điểm nào?', 'Em cần so sánh hai giao thức này trong tình huống truyền dữ liệu thực tế.'],
            ['Làm thế nào tối ưu truy vấn có nhiều JOIN?', 'Một truy vấn báo cáo đang chậm, em nên kiểm tra những yếu tố nào?'],
            ['PHP array_map và foreach nên dùng khi nào?', 'Hai cách xử lý danh sách này có khác biệt gì về khả năng đọc và hiệu năng?'],
            ['Cách thiết kế bảng lưu lịch sử thay đổi?', 'Em muốn lưu lại các lần cập nhật trạng thái của một bản ghi.'],
            ['Thuật toán BFS và DFS khác nhau ra sao?', 'Em muốn chọn thuật toán duyệt đồ thị phù hợp cho bài tập tìm đường.'],
        ];
        $authors = User::all();
        $answerBodies = [
            'Bạn có thể bắt đầu bằng cách kiểm tra tài liệu chính thức và thử trên một ví dụ nhỏ.',
            'Nên tách phần xử lý này thành từng bước để dễ kiểm thử và bảo trì.',
            'Trường hợp này phụ thuộc vào cấu trúc dữ liệu; bạn có thể chia sẻ thêm ví dụ cụ thể.',
        ];

        foreach ($samples as [$title, $body]) {
            $question = Question::firstOrCreate(
                ['title' => $title],
                [
                    'user_id' => $authors->random()->id,
                    'subject_id' => $subjects->random()->id,
                    'body' => $body,
                    'view_count' => random_int(0, 1000),
                ]
            );
           $question->tags()->sync($tags->random(2)->pluck('id')->all());

            if ($question->wasRecentlyCreated) {
                $answerCount = random_int(0, 3);
                for ($i = 0; $i < $answerCount; $i++) {
                    Answer::create([
                        'question_id' => $question->id,
                        'user_id' => $authors->random()->id,
                        'body' => $answerBodies[array_rand($answerBodies)],
                    ]);
                }
            }
        }
    }
}
