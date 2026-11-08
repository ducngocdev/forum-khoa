<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BookTest extends TestCase
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

    public function test_book_can_be_posted_without_an_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'Sách không kèm ảnh',
            'condition' => 'used',
            'type' => 'sale',
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', [
            'title' => 'Sách không kèm ảnh',
            'image_path' => null,
        ]);
    }

    public function test_book_can_be_posted_with_an_optional_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'Sách có ảnh',
            'condition' => 'used',
            'type' => 'sale',
            'image' => UploadedFile::fake()->createWithContent(
                'book.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/lS8AAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertRedirect(route('books.index'));
        $book = Book::where('title', 'Sách có ảnh')->firstOrFail();
        $this->assertNotNull($book->image_path);
        $image = storage_path('app/public/' . $book->image_path);
        $this->uploadedFiles[] = $image;
        $this->assertFileExists($image);

        $this->get(route('books.index'))->assertSee(asset('storage/' . $book->image_path));
    }
}
