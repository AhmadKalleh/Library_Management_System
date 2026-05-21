<?php

namespace App\Repositories\Interfaces;

interface BookRepositoryInterface
{
    public function index(): array;
    public function create_book(array $data): array;
    public function update_book(array $data): array;
    public function delete_book($book_id): array;
    public function global_search($value): array;
}
