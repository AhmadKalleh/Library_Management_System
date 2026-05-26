<?php

namespace App\Repositories\Interfaces;

interface BorrowRepositoryInterface
{
    public function index(string $status, bool $is_admin, int $user_id): array;
    public function request_borrow(int $book_id, int $user_id): array;
    public function confirm_receive(int $borrow_id): array;
    public function return_book(int $borrow_id): array;
}
