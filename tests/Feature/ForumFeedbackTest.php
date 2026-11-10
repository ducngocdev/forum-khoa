<?php

namespace Tests\Feature;

use App\Models\{Answer, Question, Subject, User};
use App\Services\{AcceptedAnswerService, ReputationService, VoteService};
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ForumFeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_an_answer_uses_one_consistent_pointer(): void
    {
        $asker = User::factory()->create();
        $firstAuthor = User::factory()->create();
        $secondAuthor = User::factory()->create();
        $question = $this->createQuestion($asker);
        $firstAnswer = $this->createAnswer($question, $firstAuthor);
        $secondAnswer = $this->createAnswer($question, $secondAuthor);

        $this->actingAs($asker)->post(route('answers.accept', $firstAnswer));
        $this->assertSame($firstAnswer->id, $question->fresh()->accepted_answer_id);

        $this->post(route('answers.accept', $secondAnswer));
        $this->assertSame($secondAnswer->id, $question->fresh()->accepted_answer_id);
        $this->assertSame(0, $firstAuthor->fresh()->reputation);
        $this->assertSame(15, $secondAuthor->fresh()->reputation);

        $this->post(route('answers.accept', $secondAnswer));
        $this->assertNull($question->fresh()->accepted_answer_id);
        $this->assertSame(0, $secondAuthor->fresh()->reputation);
        $this->assertSame(0, $asker->fresh()->reputation);
        $this->assertFalse(Schema::hasColumn('answers', 'is_accepted'));
    }

    public function test_database_rejects_an_accepted_answer_from_another_question(): void
    {
        $author = User::factory()->create();
        $firstQuestion = $this->createQuestion($author);
        $secondQuestion = $this->createQuestion($author);
        $answer = $this->createAnswer($secondQuestion, $author);

        $this->expectException(QueryException::class);
        $firstQuestion->update(['accepted_answer_id' => $answer->id]);
    }

    public function test_reputation_can_be_negative_and_vote_history_tracks_cancellation_and_change(): void
    {
        $author = User::factory()->create();
        $voter = User::factory()->create(['reputation' => 20]);
        $question = $this->createQuestion($author);
        ReputationService::give($author, 1, 'earned');

        VoteService::cast($voter, $question, -1);
        $this->assertSame(-1, $author->fresh()->reputation);
        $this->assertSame(0, $author->fresh()->display_reputation);

        VoteService::cast($voter, $question, -1);
        VoteService::cast($voter, $question, -1);
        VoteService::cast($voter, $question, 1);

        $this->assertSame(6, $author->fresh()->reputation);
        $this->assertSame(6, $author->reputationLogs()->sum('points'));
        $this->assertSame(1, $author->reputationLogs()->where('action', 'vote_cancelled')->count());
        $this->assertSame(1, $author->reputationLogs()->where('action', 'vote_changed')->count());
    }

    public function test_share_posts_can_receive_yes_no_helpfulness_ratings(): void
    {
        $author = User::factory()->create();
        $teacher = User::factory()->create(['academic_title' => 'Giảng viên']);
        $share = $this->createQuestion($author);
        $share->update(['post_type' => 'share']);

        $this->actingAs($teacher)->post(route('votes.store'), [
            'type' => 'question',
            'id' => $share->id,
            'value' => 1,
        ])->assertRedirect();

        $this->assertSame(0, $share->fresh()->score);
        $this->assertDatabaseHas('votes', [
            'user_id' => $teacher->id,
            'votable_type' => Question::class,
            'votable_id' => $share->id,
            'value' => 1,
        ]);
    }

    public function test_regular_questions_cannot_be_rated_as_helpful_yes_no(): void
    {
        $author = User::factory()->create();
        $teacher = User::factory()->create(['academic_title' => 'Giảng viên']);
        $question = $this->createQuestion($author);

        $this->actingAs($teacher)->post(route('votes.store'), [
            'type' => 'question',
            'id' => $question->id,
            'value' => 1,
        ])->assertRedirect();

        $this->assertSame(1, $question->fresh()->score);
    }

    public function test_deleting_an_answer_author_clears_the_accepted_answer_reference(): void
    {
        $asker = User::factory()->create();
        $author = User::factory()->create();
        $question = $this->createQuestion($asker);
        $answer = $this->createAnswer($question, $author);

        AcceptedAnswerService::toggle($question, $answer);
        $this->assertSame(2, $asker->fresh()->reputation);

        AcceptedAnswerService::clearForUser($author);
        $author->delete();

        $this->assertNull($question->fresh()->accepted_answer_id);
        $this->assertSame(0, $asker->fresh()->reputation);
    }

    public function test_deleting_a_question_owner_cascades_through_its_accepted_answer(): void
    {
        $asker = User::factory()->create();
        $author = User::factory()->create();
        $question = $this->createQuestion($asker);
        $answer = $this->createAnswer($question, $author);
        AcceptedAnswerService::toggle($question, $answer);

        DB::transaction(function () use ($asker) {
            AcceptedAnswerService::clearForUser($asker);
            $asker->delete();
        });

        $this->assertModelMissing($question);
        $this->assertModelMissing($answer);
    }

    public function test_negative_reputation_is_displayed_as_zero_in_pages_and_statistics(): void
    {
        $user = User::factory()->create(['name' => 'Negative score member', 'reputation' => -5]);
        $this->createQuestion($user);

        $this->assertSame(0, $user->fresh()->display_reputation);
        $this->assertSame(0, DB::table('v_top_contributors')
            ->where('id', $user->id)
            ->value('display_reputation'));
        $this->get('/')->assertOk()->assertSee('★ 0');
        $this->get('/questions')->assertOk()->assertSee('★ 0');
        $this->get('/stats')->assertOk()->assertSee('Negative score member');
        $this->get(route('users.show', $user))->assertOk()->assertSee('Negative score member');
    }

    private function createQuestion(User $user): Question
    {
        $subject = Subject::create([
            'code' => fake()->unique()->bothify('??###'),
            'name' => fake()->word(),
        ]);

        return Question::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'title' => 'A question title',
            'body' => 'A question body with enough detail.',
        ]);
    }

    private function createAnswer(Question $question, User $user): Answer
    {
        return Answer::create([
            'question_id' => $question->id,
            'user_id' => $user->id,
            'body' => 'An answer with enough detail.',
        ]);
    }
}
