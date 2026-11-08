<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $t) {
            $t->index(['is_hidden', 'created_at']);
            $t->index(['subject_id', 'is_hidden']);
            $t->index('score');
        });
        Schema::table('answers', fn (Blueprint $t) => $t->index(['question_id', 'is_hidden', 'score']));
        Schema::table('votes', fn (Blueprint $t) => $t->index(['user_id', 'updated_at']));
        Schema::table('reports', fn (Blueprint $t) => $t->index('status'));
        Schema::table('reputation_logs', fn (Blueprint $t) => $t->index(['user_id', 'created_at']));

        $viewPrefix = DB::getDriverName() === 'mysql' ? 'CREATE OR REPLACE VIEW' : 'CREATE VIEW';

        DB::statement("$viewPrefix v_subject_stats AS
            SELECT s.id, s.code, s.name,
                   COUNT(q.id) AS total_questions,
                   COALESCE(SUM(q.accepted_answer_id IS NOT NULL), 0) AS answered_questions,
                   COALESCE(SUM(q.accepted_answer_id IS NULL), 0) AS unanswered_questions
            FROM subjects s LEFT JOIN questions q ON q.subject_id = s.id AND q.is_hidden = 0
            GROUP BY s.id, s.code, s.name");

        DB::statement("$viewPrefix v_top_contributors AS
            SELECT u.id, u.name, u.reputation,
                   COUNT(DISTINCT q.id) AS questions,
                   COUNT(DISTINCT a.id) AS answers,
                   COUNT(DISTINCT CASE WHEN a.is_accepted = 1 THEN a.id END) AS accepted
            FROM users u
            LEFT JOIN questions q ON q.user_id = u.id AND q.is_hidden = 0
            LEFT JOIN answers a ON a.user_id = u.id AND a.is_hidden = 0
            GROUP BY u.id, u.name, u.reputation");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_top_contributors');
        DB::statement('DROP VIEW IF EXISTS v_subject_stats');
        Schema::table('reputation_logs', fn (Blueprint $t) => $t->dropIndex(['user_id', 'created_at']));
        Schema::table('reports', fn (Blueprint $t) => $t->dropIndex(['status']));
        Schema::table('votes', fn (Blueprint $t) => $t->dropIndex(['user_id', 'updated_at']));
        Schema::table('answers', fn (Blueprint $t) => $t->dropIndex(['question_id', 'is_hidden', 'score']));
        Schema::table('questions', function (Blueprint $t) {
            $t->dropIndex(['is_hidden', 'created_at']); $t->dropIndex(['subject_id', 'is_hidden']); $t->dropIndex(['score']);
        });
    }
};