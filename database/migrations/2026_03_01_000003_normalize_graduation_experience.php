<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('users')->where('study_year', 'Ra trường')->update(['study_year' => 'Đã tốt nghiệp']);
    }

    public function down(): void
    {
        DB::table('users')->where('study_year', 'Đã tốt nghiệp')->update(['study_year' => 'Ra trường']);
    }
};
