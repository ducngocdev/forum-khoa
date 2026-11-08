<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_administrators_can_view_account_management(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_administrator_can_search_and_filter_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activeUser = User::factory()->create(['name' => 'Active Member', 'email' => 'active@example.test']);
        User::factory()->create(['name' => 'Locked Member', 'is_banned' => true]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'active@example.test', 'status' => 'active']))
            ->assertOk()
            ->assertSee('Active Member')
            ->assertDontSee('Locked Member');
    }

    public function test_administrator_can_lock_and_unlock_an_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.lock', $member))
            ->assertRedirect();

        $this->assertTrue($member->refresh()->is_banned);

        $this->patch(route('admin.users.unlock', $member))
            ->assertRedirect();

        $this->assertFalse($member->refresh()->is_banned);
    }

    public function test_locked_accounts_cannot_log_in_or_continue_using_an_existing_session(): void
    {
        $member = User::factory()->create(['is_banned' => true]);

        $this->post(route('login'), [
            'email' => $member->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->actingAs($member)
            ->get(route('profile.edit'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_administrator_cannot_lock_themselves_but_can_lock_another_administrator(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $otherAdmin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->patch(route('admin.users.lock', $admin))
            ->assertSessionHasErrors('account');

        $this->patch(route('admin.users.lock', $otherAdmin))
            ->assertRedirect();

        $this->assertFalse($admin->refresh()->is_banned);
        $this->assertTrue($otherAdmin->refresh()->is_banned);
    }

    public function test_administrator_can_delete_another_account_but_not_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $member))
            ->assertRedirect();
        $this->assertNull($member->fresh());

        $this->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('account');

        $this->assertNotNull($admin->fresh());
    }
}
