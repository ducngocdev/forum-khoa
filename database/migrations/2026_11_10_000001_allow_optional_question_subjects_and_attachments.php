<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_subject_stats');
        DB::statement('DROP VIEW IF EXISTS v_top_contributors');

        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->change();
        });

        Schema::create('question_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('mime_type', 100);
            $table->timestamps();
            $table->index(['question_id', 'created_at']);
        });

        $this->createViews();
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_subject_stats');
        DB::statement('DROP VIEW IF EXISTS v_top_contributors');

        Schema::dropIfExists('question_media');

        $fallbackSubjectId = DB::table('subjects')->value('id');
        if ($fallbackSubjectId !== null) {
            DB::table('questions')->whereNull('subject_id')->update(['subject_id' => $fallbackSubjectId]);
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable(false)->change();
        });

        $this->createViews();
    }

    private function createViews(): void
    {
        DB::statement('CREATE VIEW v_top_contributors AS
            SELECT u.id, u.name, u.reputation,
                   CASE WHEN u.reputation > 0 THEN u.reputation ELSE 0 END AS display_reputation,
                   COUNT(DISTINCT q.id) AS questions,
                   COUNT(DISTINCT a.id) AS answers,
                   COUNT(DISTINCT CASE WHEN q.accepted_answer_id = a.id THEN a.id END) AS accepted
            FROM users u
            LEFT JOIN questions q ON q.user_id = u.id AND q.is_hidden = 0
            LEFT JOIN answers a ON a.user_id = u.id AND a.is_hidden = 0
            GROUP BY u.id, u.name, u.reputation');

        DB::statement('CREATE VIEW v_subject_stats AS
            SELECT s.id, s.code, s.name,
                   COUNT(q.id) AS total_questions,
                   COALESCE(SUM(q.accepted_answer_id IS NOT NULL), 0) AS answered_questions,
                   COALESCE(SUM(q.accepted_answer_id IS NULL), 0) AS unanswered_questions
            FROM subjects s LEFT JOIN questions q ON q.subject_id = s.id AND q.is_hidden = 0
            GROUP BY s.id, s.code, s.name');
    }
};
