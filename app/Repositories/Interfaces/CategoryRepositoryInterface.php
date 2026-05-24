<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function index(): array;

    public function search_category($value): array;

    public function create_category(array $data): array;

    public function update_category(array $data, $category_id): array;

    public function delete_category($category_id): array;
}