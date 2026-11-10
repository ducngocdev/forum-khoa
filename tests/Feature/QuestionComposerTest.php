<?php

namespace Tests\Feature;

use App\Models\{Question, QuestionMedia, Subject, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_question_can_be_posted_without_a_subject_and_with_an_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $image = UploadedFile::fake()->createWithContent(
            'diagram.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lS8AAAAASUVORK5CYII=')
        );

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'title' => 'How does this query work?',
            'body' => 'I have included a diagram to explain the question.',
            'attachments' => [$image],
        ]);

        $question = Question::where('title', 'How does this query work?')->firstOrFail();
        $response->assertRedirect(route('questions.show', $question));
        $this->assertNull($question->subject_id);
        $this->assertSame('question', $question->post_type);

        $media = $question->media()->firstOrFail();
        $this->assertSame('image/png', $media->mime_type);
        Storage::disk('public')->assertExists($media->path);
        $this->get(route('questions.show', $question))
            ->assertOk()
            ->assertSee('Ảnh đính kèm của câu hỏi')
            ->assertSee('data-image-viewer', false)
            ->assertSee('data-history-back', false)
            ->assertDontSee('target="_blank"', false);
        $this->get(route('questions.index'))
            ->assertOk()
            ->assertSee($question->title)
            ->assertSee('question-feed-card__media', false);
        $this->get(route('home'))
            ->assertOk()
            ->assertSee($question->title)
            ->assertSee('question-feed-card__media', false);
    }

    public function test_question_can_be_posted_with_a_video_attachment(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $video = UploadedFile::fake()->create('walkthrough.mp4', 512, 'video/mp4');

        $this->actingAs($user)->post(route('questions.store'), [
            'title' => 'How do I reproduce this issue?',
            'body' => 'This short recording shows the issue I am seeing.',
            'attachments' => [$video],
        ])->assertRedirect();

        $media = QuestionMedia::firstOrFail();
        $this->assertSame('video/mp4', $media->mime_type);
        $this->assertTrue($media->is_video);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_question_composer_has_cancel_action_optional_subject_and_attachment_controls(): void
    {
        Subject::create(['code' => 'CS101', 'name' => 'Computer Science']);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('questions.create'))
            ->assertOk()
            ->assertSee('Hủy và quay lại')
            ->assertSee('không bắt buộc')
            ->assertSee('attachments[]')
            ->assertSee('name="post_type" value="question"', false)
            ->assertSee('name="post_type" value="share"', false)
            ->assertSee('Đăng bài')
            ->assertSee('id="question-title"', false)
            ->assertSee('id="question-body"', false)
            ->assertDontSee('data-writing-field', false);
    }

    public function test_share_posts_are_saved_and_use_comments_instead_of_answers(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('questions.store'), [
            'post_type' => 'share',
            'title' => 'My useful study notes',
            'body' => 'Here are some notes that may help other students.',
        ]);

        $question = Question::where('title', 'My useful study notes')->firstOrFail();
        $this->assertSame('share', $question->post_type);
        $response->assertRedirect(route('questions.show', $question));

        $this->actingAs($user)->get(route('questions.show', $question))
            ->assertOk()
            ->assertSee('Bài chia sẻ')
            ->assertSee('Đánh giá Yes')
            ->assertSee('Đánh giá No')
            ->assertSee('id="post-comments"', false)
            ->assertDontSee('id="answer-form"', false);

        $this->get(route('questions.index'))
            ->assertOk()
            ->assertSee('Đánh giá Yes')
            ->assertSee('Đánh giá No');
    }

    public function test_question_list_can_be_filtered_by_post_type(): void
    {
        $user = User::factory()->create();
        $question = Question::create([
            'user_id' => $user->id,
            'title' => 'A question to filter by type',
            'body' => 'This is a question body long enough for the test.',
            'post_type' => 'question',
        ]);
        $share = Question::create([
            'user_id' => $user->id,
            'title' => 'A share post to filter by type',
            'body' => 'This is a shared post body long enough for the test.',
            'post_type' => 'share',
        ]);

        $this->get(route('questions.index', ['post_type' => 'share']))
            ->assertOk()
            ->assertSee($share->title)
            ->assertDontSee($question->title)
            ->assertSee('value="share" selected', false);

        $this->get(route('questions.index', ['post_type' => 'question']))
            ->assertOk()
            ->assertSee($question->title)
            ->assertDontSee($share->title)
            ->assertSee('value="question" selected', false);
    }

    public function test_question_attachments_are_deleted_when_the_author_is_deleted(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $subject = Subject::create(['code' => 'CS102', 'name' => 'Databases']);
        $question = Question::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'title' => 'A question with an attachment',
            'body' => 'Some explanatory content for this question.',
        ]);
        Storage::disk('public')->put('questions/' . $question->id . '/image.png', 'image');
        $media = $question->media()->create([
            'path' => 'questions/' . $question->id . '/image.png',
            'mime_type' => 'image/png',
        ]);

        $user->delete();

        Storage::disk('public')->assertMissing($media->path);
        $this->assertDatabaseMissing('question_media', ['id' => $media->id]);
    }
}
