<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    private function categoriesData($categories)
    {
        return [
            'categories' => $categories->items(),

            'pagination' => [
                'current_page'  => $categories->currentPage(),
                'last_page'     => $categories->lastPage(),
                'per_page'      => $categories->perPage(),
                'total'         => $categories->total(),
                'has_more'      => $categories->hasMorePages(),
                'next_page_url' => $categories->nextPageUrl(),
                'prev_page_url' => $categories->previousPageUrl(),
            ],
        ];
    }

    public function index(): array
    {
        $categories = Category::withCount('books')
            ->latest()
            ->paginate(10);

        return $this->categoriesData($categories);
    }

    public function search_category($value): array
    {
        $categories = Category::where('name', 'LIKE', "%{$value}%")
            ->paginate(10);

        return $this->categoriesData($categories);
    }

    public function create_category(array $data): array
    {
        $category = Category::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return [
            'category' => $category
        ];
    }

    public function update_category(array $data, $category_id): array
    {
        $category = Category::findOrFail($category_id);

        $category->update([
            'name'        => $data['name']        ?? $category->name,
            'description' => $data['description'] ?? $category->description,
        ]);

        return [
            'category' => $category
        ];
    }

    public function delete_category($category_id): array
    {
        $category = Category::withCount('books')
            ->findOrFail($category_id);

            // to not allow deleting a category if it has books, you can change this logic as you want 
        if ($category->books_count > 0) {

            return [
                'status' => 'has_books'
            ];
        }

        $category->delete();

        return [
            'status' => 'deleted'
        ];
    }
}