<?php

namespace App\Http\Controllers\Api\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequests\FormRequestBook;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\Book\BookService;
use App\Traits\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;

class BookController extends Controller
{
    use ResponseHelper;

    public function __construct(
        protected BookService $_bookService
    ) {}

    public function index(FormRequestBook $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('viewAny', Book::class);
            $raw  = $this->_bookService->get_all_books($request->validated());
            $data = [
                'books'      =>BookResource::collection($raw['data']['books']),
                'pagination' => $raw['data']['pagination'],
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function global_search(FormRequestBook $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('view', Book::class);
            $raw  = $this->_bookService->search_books($request->validated());
            $data = [
                'books'      => BookResource::collection($raw['data']['books']),
                'pagination' => $raw['data']['pagination'],
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function show_book(FormRequestBook $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('view', Book::class);
            $raw  = $this->_bookService->show_book($request->validated()['book_id']);
            $data = ['book' => new BookResource($raw['data']['book'])];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function filter(FormRequestBook $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('view', Book::class);
            $raw  = $this->_bookService->filter_books($request->validated());
            $data = [
                'books'      => BookResource::collection($raw['data']['books']),
                'pagination' => $raw['data']['pagination'],
            ];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function create_book(FormRequestBook $request): JsonResponse
    {
        $data = [];

        try {
            $this->authorize('create', Book::class);

            $raw  = $this->_bookService->create_book($request->validated());
            $data = ['book' => new BookResource($raw['data']['book'])];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function update_book(FormRequestBook $request): JsonResponse
    {
        $data = [];

        try {

            $this->authorize('update', Book::class);

            $raw  = $this->_bookService->update_book($request->validated());
            $data = ['book' => new BookResource($raw['data']['book'])];

            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }

    public function delete_book(FormRequestBook $request): JsonResponse
    {
        $data = [];

        try {

            $this->authorize('delete', Book::class);

            $raw  = $this->_bookService->delete_book($request->validated());
            return $this->Success($data, $raw['message'], $raw['code']);

        } catch (\Illuminate\Auth\Access\AuthorizationException) {
            return $this->Error($data, 'Unauthorized', 403);

        } catch (Throwable $e) {
            return $this->Error($data, $e->getMessage());
        }
    }
}
