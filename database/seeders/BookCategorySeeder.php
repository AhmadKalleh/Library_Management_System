<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Database\Seeder;

class BookCategorySeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء Categories
        $categories = [
            ['name' => 'Programming','description' => 'Books about programming and software development.'],
            ['name' => 'Science','description' => 'Books about science and research.'],
            ['name' => 'Literature','description' => 'Books about literature and writing.'],
            ['name' => 'History','description' => 'Books about historical events and figures.'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name'],'description' => $cat['description']]);
        }

        $books = [
            [
                'title'            => 'Clean Code',
                'author'           => 'Robert C. Martin',
                'category_id'      => 1,
                'available_copies' => 5,
                'borrowed_copies'  => 2,
                'status'           => 'available',
            ],
            [
                'title'            => 'The Pragmatic Programmer',
                'author'           => 'Andrew Hunt',
                'category_id'      => 1,
                'available_copies' => 3,
                'borrowed_copies'  => 1,
                'status'           => 'available',
            ],
            [
                'title'            => 'A Brief History of Time',
                'author'           => 'Stephen Hawking',
                'category_id'      => 2,
                'available_copies' => 4,
                'borrowed_copies'  => 0,
                'status'           => 'available',
            ],
            [
                'title'            => 'The Great Gatsby',
                'author'           => 'F. Scott Fitzgerald',
                'category_id'      => 3,
                'available_copies' => 0,
                'borrowed_copies'  => 3,
                'status'           => 'unavailable',
            ],
            [
                'title'            => 'Sapiens',
                'author'           => 'Yuval Noah Harari',
                'category_id'      => 4,
                'available_copies' => 6,
                'borrowed_copies'  => 1,
                'status'           => 'available',
            ],
            [
                'title' => 'The Lord of the Rings',
                'author' => 'J.R.R. Tolkien',
                'category_id' => 4,
                'available_copies' => 3,
                'borrowed_copies' => 2,
                'status' => 'available',
            ],

            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'category_id' => 3,
                'available_copies' => 2,
                'borrowed_copies' => 1,
                'status' => 'available',
            ],

            [
                'title' => 'Meditations',
                'author' => 'Marcus Aurelius',
                'category_id' => 2,
                'available_copies' => 6,
                'borrowed_copies' => 1,
                'status' => 'available',
            ],
            [
                'title' => 'Zero to One',
                'author' => 'Peter Thiel',
                'category_id' => 4,
                'available_copies' => 5,
                'borrowed_copies' => 1,
                'status' => 'available',
            ],

            [
                'title' => 'The Alchemist',
                'author' => 'Paulo Coelho',
                'category_id' => 4,
                'available_copies' => 8,
                'borrowed_copies' => 2,
                'status' => 'available',
            ],

            [
                'title' => 'Harry Potter and the Sorcerer\'s Stone',
                'author' => 'J.K. Rowling',
                'category_id' => 3,
                'available_copies' => 10,
                'borrowed_copies' => 5,
                'status' => 'available',
            ],

            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'category_id' => 1,
                'available_copies' => 4,
                'borrowed_copies' => 1,
                'status' => 'available',
            ],
        ];

        foreach ($books as $bookData) {
            $book = Book::firstOrCreate(
                ['title' => $bookData['title']],
                $bookData
            );

            // نفس الصورة لكل الكتب تجريبياً
            if (!$book->image) {
                $book->image()->create([
                    'path' => 'books/book-image.png',
                ]);
            }
        }
    }
}
