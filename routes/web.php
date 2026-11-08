<?php
use App\Http\Controllers\{AccountManagementController, AnswerController, BookController, CommentController, HomeController, ModerationController, ProfileController, QuestionController, ReportController, StatsController, UserController, VoteController};
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
Route::get('/stats', [StatsController::class, 'index'])->name('stats');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

Route::middleware(['auth', 'not_banned'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('questions.index'))->name('dashboard');
    Route::get('/admin/users', [AccountManagementController::class, 'index'])->name('admin.users.index');
    Route::patch('/admin/users/{user}/lock', [AccountManagementController::class, 'lock'])->name('admin.users.lock');
    Route::patch('/admin/users/{user}/unlock', [AccountManagementController::class, 'unlock'])->name('admin.users.unlock');
    Route::delete('/admin/users/{user}', [AccountManagementController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::get('/questions/similar', [QuestionController::class, 'similar'])->name('questions.similar');
    Route::post('/questions', [QuestionController::class, 'store'])->middleware('throttle:5,1')->name('questions.store');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store'])->middleware('throttle:10,1')->name('answers.store');
    Route::delete('/answers/{answer}', [AnswerController::class, 'destroy'])->name('answers.destroy');
    Route::post('/answers/{answer}/accept', [AnswerController::class, 'accept'])->name('answers.accept');
    Route::post('/comments', [CommentController::class, 'store'])->middleware('throttle:20,1')->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/votes', [VoteController::class, 'store'])->name('votes.store');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::patch('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation');
    Route::post('/moderation/{report}/hide', [ModerationController::class, 'hide'])->name('moderation.hide');
    Route::post('/moderation/{report}/dismiss', [ModerationController::class, 'dismiss'])->name('moderation.dismiss');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('questions.show');

require __DIR__ . '/auth.php';
