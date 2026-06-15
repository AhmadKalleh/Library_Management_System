<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use App\Traits\Upload\UplodeImageHelper;
use Illuminate\Support\Facades\Storage;

class BookRepository implements BookRepositoryInterface
{
    // Implement book-related data access methods here

    use UplodeImageHelper;
    private function booksData($books)
    {
        return [
            'books'      => $books->items(),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page'    => $books->lastPage(),
                'per_page'     => $books->perPage(),
                'total'        => $books->total(),
                'has_more'     => $books->hasMorePages(),
                'next_page_url' => $books->nextPageUrl(),
                'prev_page_url' => $books->previousPageUrl(),
            ],
        ];
    }

    public function show_book($book_id): array
    {
        $book = Book::with(['category', 'image'])->findOrFail($book_id);

        return ['book' => $book];
    }

    public function index($category_name): array
    {

        switch (strtolower($category_name)) {
            case 'all':
                $books = Book::with(['category', 'image'])
                    ->latest()
                    ->paginate(10);
                break;

            default:
                $books = Book::with(['category', 'image'])
                    ->whereHas('category', function ($query) use ($category_name) {
                        $query->where('name', $category_name);
                    })
                    ->latest()
                    ->paginate(10);
                break;
        }

        return $this->booksData($books);
    }

    public function filter($category_id = null, $status = 'all'): array
    {
        $query = Book::with(['category', 'image']);

        // ✅ فلترة حسب التصنيف فقط إذا موجود
        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        // ✅ فلترة حسب الحالة
        switch (strtolower($status)) {
            case 'available':
                $query->where('available_copies', '>', 0);
                break;

            case 'unavailable':
                $query->where('available_copies', '=', 0);
                break;

            case 'all':
            default:
                // لا شيء
                break;
        }

        $books = $query->latest()->paginate(10);

        return $this->booksData($books);
    }

    public function global_search($value): array
    {
        $books = Book::with(['category', 'image'])

        ->where(function ($query) use ($value)
        {
            $query->where('title', 'LIKE', "%{$value}%")
                ->orWhere('author', 'LIKE', "%{$value}%")
                ->orWhereHas('category', function ($q) use ($value) {

                    $q->where('name', 'LIKE', "%{$value}%");
                });
        })

        ->paginate(10);

        return $this->booksData($books);
    }

    public function create_book(array $data): array
    {
        $book = Book::create([
            'title'            => $data['title'],
            'author'           => $data['author'],
            'category_id'      => $data['category_id'],
            'available_copies' => $data['available_copies'],
        ]);

        if (!empty($data['image'])) {

            $book->image()->create(['path' => $this->uplodeImage($data['image'],'books')]);
        }

        return ['book' => $book->load(['category', 'image'])];
    }

    public function update_book(array $data): array
    {
        $book = Book::with(['image'])->findOrFail($data['book_id']);

        $book->update([
            'title'            => $data['title']            ?? $book->title,
            'author'           => $data['author']           ?? $book->author,
            'available_copies' => $data['available_copies'] ?? $book->available_copies,
            'status'           => ($data['available_copies'] ?? $book->available_copies) > 0 ? 'available' : 'unavailable',
        ]);

        // تحديث الصورة إذا أُرسلت
        if (!empty($data['image'])) {
            if ($book->image && Storage::disk('public')->exists($book->image->path))
            {
                Storage::disk('public')->delete($book->image->path);
                $book->image()->delete();
            }
            $path = $this->uplodeImage($data['image'],'books');
            $book->image()->create(['path' => $path]);
        }

        return ['book' => $book->load(['category', 'image'])];
    }

    public function delete_book($book_id): array
    {
        $book = Book::findOrFail($book_id);

        if ($book->borrowed_copies > 0) {
            return ['status' => 'has_borrowed'];
        }

        if ($book->image && Storage::disk('public')->exists($book->image->path))
        {
            Storage::disk('public')->delete($book->image->path);
            $book->image()->delete();
        }

        $book->delete();
        return ['status' => 'deleted'];
    }
}
