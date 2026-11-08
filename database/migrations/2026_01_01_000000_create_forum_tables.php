<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role')->default('student');      // student | moderator | admin
            $t->integer('reputation')->default(0);
            $t->boolean('is_banned')->default(false);
        });
        Schema::create('subjects', function (Blueprint $t) {
            $t->id(); $t->string('code')->unique(); $t->string('name'); $t->timestamps();
        });
        Schema::create('questions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('subject_id')->constrained();
            $t->string('title'); $t->text('body');
            $t->unsignedBigInteger('accepted_answer_id')->nullable();
            $t->unsignedInteger('view_count')->default(0);
            $t->integer('score')->default(0);
            $t->boolean('is_hidden')->default(false);
            $t->timestamps();
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $t->fullText(['title', 'body']);
            }
        });
        Schema::create('answers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('question_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->text('body'); $t->integer('score')->default(0);
            $t->boolean('is_accepted')->default(false);
            $t->boolean('is_hidden')->default(false);
            $t->timestamps();
        });
        Schema::create('comments', function (Blueprint $t) {
            $t->id(); $t->morphs('commentable');
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->text('body'); $t->boolean('is_hidden')->default(false); $t->timestamps();
        });
        Schema::create('tags', function (Blueprint $t) {
            $t->id(); $t->string('name')->unique(); $t->string('slug')->unique(); $t->timestamps();
        });
        Schema::create('question_tag', function (Blueprint $t) {
            $t->foreignId('question_id')->constrained()->cascadeOnDelete();
            $t->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $t->primary(['question_id', 'tag_id']);
        });
        Schema::create('votes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->morphs('votable');
            $t->tinyInteger('value');
            $t->boolean('is_suspicious')->default(false);
            $t->timestamps();
            $t->unique(['user_id', 'votable_type', 'votable_id']);
        });
        Schema::create('reputation_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('action'); $t->integer('points'); $t->nullableMorphs('source'); $t->timestamps();
        });
        Schema::create('reports', function (Blueprint $t) {
            $t->id();
            $t->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $t->morphs('reportable');
            $t->string('reason');
            $t->string('status')->default('pending');    // pending | resolved | rejected
            $t->unsignedBigInteger('handled_by')->nullable();
            $t->timestamps();
            $t->unique(['reporter_id', 'reportable_type', 'reportable_id']);
        });
    }
    public function down(): void
    {
        foreach (['reports','reputation_logs','votes','question_tag','tags','comments','answers','questions','subjects'] as $tb) Schema::dropIfExists($tb);
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['role', 'reputation', 'is_banned']));
    }
};
