<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_top_contributors');
        DB::statement('DROP VIEW IF EXISTS v_subject_stats');

        if (Schema::hasColumn('answers', 'is_accepted')) {
            $answers = DB::table('answers')->get(['id', 'question_id'])->keyBy('id');
            $legacyAccepted = DB::table('answers')
                ->where('is_accepted', true)
                ->orderBy('id')
                ->get(['id', 'question_id'])
                ->groupBy('question_id');

            foreach (DB::table('questions')->get(['id', 'accepted_answer_id']) as $question) {
                $acceptedAnswerId = $question->accepted_answer_id;
                $acceptedAnswer = $acceptedAnswerId ? $answers->get($acceptedAnswerId) : null;

                if (! $acceptedAnswer || (int) $acceptedAnswer->question_id !== (int) $question->id) {
                    $acceptedAnswerId = $legacyAccepted->get($question->id)?->first()?->id;
                }

                DB::table('questions')->where('id', $question->id)->update([
                    'accepted_answer_id' => $acceptedAnswerId,
                ]);
            }

            Schema::table('answers', function (Blueprint $table) {
                $table->dropColumn('is_accepted');
            });
            Schema::table('answers', function (Blueprint $table) {
                $table->unique(['question_id', 'id'], 'answers_question_id_id_unique');
            });
            Schema::table('questions', function (Blueprint $table) {
                $table->foreign(['id', 'accepted_answer_id'], 'questions_accepted_answer_same_question_foreign')
                    ->references(['question_id', 'id'])
                    ->on('answers');
            });
        }
        if (! $this->hasHandlerForeignKey()) {
            Schema::table('reports', function (Blueprint $table) {
                $table->foreign('handled_by', 'reports_handled_by_foreign')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            });
        }

        $viewPrefix = DB::getDriverName() === 'mysql' ? 'CREATE OR REPLACE VIEW' : 'CREATE VIEW';
        DB::statement("$viewPrefix v_top_contributors AS
            SELECT u.id, u.name, u.reputation,
                   CASE WHEN u.reputation > 0 THEN u.reputation ELSE 0 END AS display_reputation,
                   COUNT(DISTINCT q.id) AS questions,
                   COUNT(DISTINCT a.id) AS answers,
                   COUNT(DISTINCT CASE WHEN q.accepted_answer_id = a.id THEN a.id END) AS accepted
            FROM users u
            LEFT JOIN questions q ON q.user_id = u.id AND q.is_hidden = 0
            LEFT JOIN answers a ON a.user_id = u.id AND a.is_hidden = 0
            GROUP BY u.id, u.name, u.reputation");
        DB::statement("$viewPrefix v_subject_stats AS
            SELECT s.id, s.code, s.name,
                   COUNT(q.id) AS total_questions,
                   COALESCE(SUM(q.accepted_answer_id IS NOT NULL), 0) AS answered_questions,
                   COALESCE(SUM(q.accepted_answer_id IS NULL), 0) AS unanswered_questions
            FROM subjects s LEFT JOIN questions q ON q.subject_id = s.id AND q.is_hidden = 0
            GROUP BY s.id, s.code, s.name");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_top_contributors');
        DB::statement('DROP VIEW IF EXISTS v_subject_stats');

        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign('reports_handled_by_foreign');
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign('questions_accepted_answer_same_question_foreign');
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->dropUnique('answers_question_id_id_unique');
        });
        Schema::table('answers', function (Blueprint $table) {
            $table->boolean('is_accepted')->default(false);
        });
        DB::table('answers')
            ->whereIn('id', DB::table('questions')->select('accepted_answer_id')->whereNotNull('accepted_answer_id'))
            ->update(['is_accepted' => true]);

        $viewPrefix = DB::getDriverName() === 'mysql' ? 'CREATE OR REPLACE VIEW' : 'CREATE VIEW';
        DB::statement("$viewPrefix v_top_contributors AS
            SELECT u.id, u.name, u.reputation,
                   COUNT(DISTINCT q.id) AS questions,
                   COUNT(DISTINCT a.id) AS answers,
                   COUNT(DISTINCT CASE WHEN a.is_accepted = 1 THEN a.id END) AS accepted
            FROM users u
            LEFT JOIN questions q ON q.user_id = u.id AND q.is_hidden = 0
            LEFT JOIN answers a ON a.user_id = u.id AND a.is_hidden = 0
            GROUP BY u.id, u.name, u.reputation");
        DB::statement("$viewPrefix v_subject_stats AS
            SELECT s.id, s.code, s.name,
                   COUNT(q.id) AS total_questions,
                   COALESCE(SUM(q.accepted_answer_id IS NOT NULL), 0) AS answered_questions,
                   COALESCE(SUM(q.accepted_answer_id IS NULL), 0) AS unanswered_questions
            FROM subjects s LEFT JOIN questions q ON q.subject_id = s.id AND q.is_hidden = 0
            GROUP BY s.id, s.code, s.name");
    }

    private function hasHandlerForeignKey(): bool
    {
        return DB::getDriverName() === 'sqlite'
            && collect(DB::select('PRAGMA foreign_key_list(reports)'))
                ->contains(fn ($foreignKey) => $foreignKey->from === 'handled_by');
    }
};
