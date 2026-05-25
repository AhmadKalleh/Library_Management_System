<?php

namespace App\Services\Category;

use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(
        protected CategoryRepositoryInterface $_categoryRepository
    ) {}

    public function get_all_categories(): array
    {
        $result = $this->_categoryRepository->index();

        return [
            'data'    => $result,
            'message' => 'Categories retrieved successfully',
            'code'    => 200,
        ];
    }

    
    public function create_category(array $data): array
    {
        $result = $this->_categoryRepository->create_category($data);

        return [
            'data'    => $result,
            'message' => 'Category created successfully',
            'code'    => 201,
        ];
    }

    public function update_category(array $data): array
    {
        $result = $this->_categoryRepository->update_category(
            $data,
            $data['category_id']
        );

        return [
            'data'    => $result,
            'message' => 'Category updated successfully',
            'code'    => 200,
        ];
    }

    public function delete_category(array $data): array
    {
        $result = $this->_categoryRepository->delete_category($data['category_id']);

        if ($result['status'] === 'has_books') {
            return [
                'data'    => [],
                'message' => 'Cannot delete category while it has books',
                'code'    => 409,
            ];
        }

        return [
            'data'    => [],
            'message' => 'Category deleted successfully',
            'code'    => 200,
        ];
    }
}
