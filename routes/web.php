<?php
use App\Http\Controllers\{AnswerController, CommentController, HomeController, ModerationController, ProfileController, QuestionController, ReportController, StatsController, UserController, VoteController};
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
Route::get('/stats', [StatsController::class, 'index'])->name('stats');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('questions.index'))->name('dashboard');
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::get('/questions/similar', [QuestionController::class, 'similar'])->name('questions.similar');
    Route::post('/questions', [QuestionController::class, 'store'])->middleware('throttle:5,1')->name('questions.store');
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])->middleware('throttle:10,1')->name('answers.store');
    Route::post('/answers/{answer}/accept', [AnswerController::class, 'accept'])->name('answers.accept');
    Route::post('/comments', [CommentController::class, 'store'])->middleware('throttle:20,1')->name('comments.store');
    Route::post('/votes', [VoteController::class, 'store'])->name('votes.store');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation');
    Route::post('/moderation/{report}/hide', [ModerationController::class, 'hide'])->name('moderation.hide');
    Route::post('/moderation/{report}/dismiss', [ModerationController::class, 'dismiss'])->name('moderation.dismiss');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');

require __DIR__ . '/auth.php';
