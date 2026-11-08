<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Closure;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $books = Book::with('user')
            ->where('status', 'available')
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->string('type')))
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request) {
                $term = '%' . $request->string('q') . '%';
                $query->where('title', 'like', $term)->orWhere('author', 'like', $term);
            }))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('books.index', compact('books'));
    }

    public function create(): View
    {
        return view('books.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request->file('image'));
        }
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'available';
        Book::create($data);

        return redirect()->route('books.index')->with('status', 'book-created');
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        abort_unless($book->user_id === $request->user()->id, 403);
        $data = $this->validated($request);
        unset($data['image']);

        if ($request->hasFile('image')) {
            $oldImagePath = $book->image_path;
            $data['image_path'] = $this->storeImage($request->file('image'));
            $book->update($data);
            if ($oldImagePath) {
                File::delete(storage_path('app/public/' . $oldImagePath));
            }
        } else {
            $book->update($data);
        }

        return redirect()->route('profile.edit')->with('status', 'book-updated');
    }

    public function destroy(Request $request, Book $book): RedirectResponse
    {
        abort_unless($book->user_id === $request->user()->id, 403);
        $imagePath = $book->image_path;
        $book->delete();
        if ($imagePath) {
            File::delete(storage_path('app/public/' . $imagePath));
        }

        return redirect()->route('profile.edit')->with('status', 'book-deleted');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'condition' => ['required', 'in:new,good,used'],
            'price' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'type' => ['required', 'in:sale,giveaway'],
            'status' => ['sometimes', 'in:available,sold'],
            'contact' => ['nullable', 'string', 'max:255'],
            'image' => [
                'nullable',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (!$value) {
                        return;
                    }

                    if (!$value->isValid()) {
                        $fail('Không thể tải ảnh sách lên. Vui lòng chọn lại tệp ảnh.');
                        return;
                    }

                    if ($value->getSize() > 5 * 1024 * 1024) {
                        $fail('Ảnh sách không được lớn hơn 5MB.');
                        return;
                    }

                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $extension = strtolower($value->getClientOriginalExtension());
                    $imageInfo = @getimagesize($value->getRealPath());

                    if (!in_array($extension, $allowedExtensions, true) || $imageInfo === false) {
                        $fail('Ảnh sách phải là tệp JPG, PNG, GIF hoặc WEBP hợp lệ.');
                    }
                },
            ],
        ]);
    }

    private function storeImage(UploadedFile $image): string
    {
        $extension = strtolower($image->getClientOriginalExtension());
        $directory = storage_path('app/public/books');
        File::ensureDirectoryExists($directory);

        $filename = Str::random(40) . '.' . $extension;
        $image->move($directory, $filename);

        return 'books/' . $filename;
    }
}
