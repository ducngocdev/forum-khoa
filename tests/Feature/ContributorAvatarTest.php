<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributorAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_contributor_list_uses_member_avatars(): void
    {
        $user = User::factory()->create([
            'name' => 'Contributor With Avatar',
            'avatar_path' => 'avatars/contributor.png',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('src="http://localhost/storage/avatars/contributor.png"', false)
            ->assertSee('Ảnh đại diện của Contributor With Avatar');
    }

    public function test_question_list_top_users_use_member_avatars(): void
    {
        User::factory()->create([
            'name' => 'Question List Contributor',
            'avatar_path' => 'avatars/question-contributor.png',
        ]);

        $response = $this->get(route('questions.index'));

        $response->assertOk()
            ->assertSee('src="http://localhost/storage/avatars/question-contributor.png"', false);
    }
}
