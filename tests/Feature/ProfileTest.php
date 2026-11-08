<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private array $uploadedFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->uploadedFiles as $path) {
            File::delete($path);
        }

        parent::tearDown();
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.show', $user));

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.show', $user));

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_profile_avatar_can_be_uploaded_and_replaces_the_old_file(): void
    {
        $user = User::factory()->create(['avatar_path' => 'avatars/old-avatar.png']);
        $oldAvatar = storage_path('app/public/avatars/old-avatar.png');
        File::ensureDirectoryExists(dirname($oldAvatar));
        File::put($oldAvatar, 'old avatar');
        $this->uploadedFiles[] = $oldAvatar;

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->createWithContent(
                'avatar.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lS8AAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertRedirect(route('users.show', $user));
        $this->assertNotSame('avatars/old-avatar.png', $user->refresh()->avatar_path);
        $newAvatar = storage_path('app/public/' . $user->avatar_path);
        $this->uploadedFiles[] = $newAvatar;
        $this->assertFileExists($newAvatar);
        $this->assertFileDoesNotExist($oldAvatar);
    }

    public function test_user_can_remove_their_avatar(): void
    {
        $user = User::factory()->create(['avatar_path' => 'avatars/avatar-to-remove.png']);
        $avatarPath = storage_path('app/public/avatars/avatar-to-remove.png');
        File::ensureDirectoryExists(dirname($avatarPath));
        File::put($avatarPath, 'avatar');
        $this->uploadedFiles[] = $avatarPath;

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'remove_avatar' => '1',
        ]);

        $response->assertRedirect(route('users.show', $user));
        $this->assertNull($user->refresh()->avatar_path);
        $this->assertFileDoesNotExist($avatarPath);
    }

    public function test_broken_avatar_image_has_an_initial_fallback(): void
    {
        $user = User::factory()->create([
            'name' => 'Avatar Test',
            'avatar_path' => 'avatars/missing-avatar.png',
        ]);

        $response = $this->get(route('users.show', $user));

        $response->assertOk()
            ->assertSee('onerror="this.remove()"', false)
            ->assertSee('A', false);
    }

    public function test_invalid_avatar_value_returns_a_validation_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => 'not-an-uploaded-file',
            ]);

        $response->assertSessionHasErrors('avatar');
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
