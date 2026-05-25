<?php

namespace App\Services\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;

class BookService
{
    public function __construct(
        protected BookRepositoryInterface $_bookRepository
    ) {}

    public function get_all_books(array $data): array
    {
        $result = $this->_bookRepository->index($data['category_name']);

        return [
            'data'    => $result,
            'message' => 'Books retrieved successfully',
            'code'    => 200,
        ];
    }

    public function show_book(int $book_id): array
    {
        $result = $this->_bookRepository->show_book($book_id);

        return [
            'data'    => $result,
            'message' => 'Book details retrieved successfully',
            'code'    => 200,
        ];
    }

    public function search_books(array $data): array
    {
        $result = $this->_bookRepository->global_search($data['value']);

        return [
            'data'    => $result,
            'message' => 'Search results retrieved successfully',
            'code'    => 200,
        ];
    }

    public function create_book(array $data): array
    {
        $result = $this->_bookRepository->create_book($data);

        return [
            'data'    => $result,
            'message' => 'Book created successfully',
            'code'    => 201,
        ];
    }

    public function update_book(array $data): array
    {
        $result = $this->_bookRepository->update_book($data);

        return [
            'data'    => $result,
            'message' => 'Book updated successfully',
            'code'    => 200,
        ];
    }

    public function delete_book(array $data): array
    {
        $result = $this->_bookRepository->delete_book($data['book_id']);

        if ($result['status'] === 'has_borrowed') {
            return [
                'data'    => [],
                'message' => 'Cannot delete book while copies are borrowed',
                'code'    => 409,
            ];
        }

        return [
            'data'    => [],
            'message' => 'Book deleted successfully',
            'code'    => 200,
        ];
    }
}
