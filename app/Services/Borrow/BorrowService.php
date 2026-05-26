<?php

namespace App\Services\Borrow;
use App\Repositories\Interfaces\BorrowRepositoryInterface;

class BorrowService
{
    public function __construct(
        protected BorrowRepositoryInterface $_borrowRepository
    ) {}

    public function get_borrows(string $status, bool $is_admin, int $user_id): array
    {
        $result = $this->_borrowRepository->index($status, $is_admin, $user_id);

        return [
            'data'    => $result,
            'message' => 'Borrows retrieved successfully',
            'code'    => 200,
        ];
    }

    public function request_borrow(int $book_id, int $user_id): array
    {
        $result = $this->_borrowRepository->request_borrow($book_id, $user_id);

        return match($result['status']) {
            'overdue'          => ['data' => [], 'message' => 'You have an overdue book, please return it first', 'code' => 403],
            'max_borrows'      => ['data' => [], 'message' => 'You cannot borrow more than 2 books at the same time', 'code' => 403],
            'unavailable'      => ['data' => [], 'message' => 'This book is not available', 'code' => 409],
            'already_requested'=> ['data' => [], 'message' => 'You already have an active request for this book', 'code' => 409],
            'inactive_user'    => ['data' => [], 'message' => 'Your account is inactive, please contact the library', 'code' => 403],
            'success'          => ['data' => $result['borrow'], 'message' => 'Borrow request sent successfully', 'code' => 201],
        };
    }

    public function confirm_receive(int $borrow_id): array
    {
        $result = $this->_borrowRepository->confirm_receive($borrow_id);

        return match($result['status']) {
            'not_pending' => ['data' => [], 'message' => 'Only pending borrows can be confirmed', 'code' => 400],
            'confirmed' => ['data' => [], 'message' => 'Borrow confirmed successfully', 'code' => 200],
        };
    }

    public function return_book(int $borrow_id): array
    {
        $result = $this->_borrowRepository->return_book($borrow_id);

        return match($result['status']) {
            'not_borrowed' => ['data' => [], 'message' => 'Only borrowed books can be returned', 'code' => 400],
            'returned' => ['data' => [], 'message' => 'Book returned successfully', 'code' => 200],
        };

    }
}
