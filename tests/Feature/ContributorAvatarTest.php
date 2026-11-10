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

    public function test_role_specific_avatar_frames_and_role_badges_are_rendered(): void
    {
        $administrator = User::factory()->create([
            'name' => 'Administrator Member',
            'role' => 'admin',
        ]);
        \App\Models\Question::create([
            'user_id' => $administrator->id,
            'title' => 'A post by an administrator',
            'body' => 'This administrator post has enough detail for a card.',
        ]);
        $educator = User::factory()->create([
            'name' => 'Faculty Member',
            'academic_title' => 'Giảng viên',
        ]);
        \App\Models\Question::create([
            'user_id' => $educator->id,
            'title' => 'A post by a faculty member',
            'body' => 'This faculty post has enough detail for a card.',
        ]);

        $this->get(route('questions.index'))
            ->assertOk()
            ->assertSee('user-avatar--admin', false)
            ->assertSee('user-role-badge--admin', false)
            ->assertSee('Quản trị')
            ->assertSee('user-avatar--educator', false)
            ->assertSee('user-role-badge--educator', false)
            ->assertSee('Giảng viên');
    }

    public function test_navigation_exposes_a_persistent_light_dark_theme_toggle(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-theme-toggle', false)
            ->assertSee("localStorage.getItem('forum-theme')", false)
            ->assertSee('Sáng/Tối');
    }
}
