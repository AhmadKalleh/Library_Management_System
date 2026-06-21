<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\User;
use App\Repositories\Interfaces\BorrowRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BorrowRepository implements BorrowRepositoryInterface
{
    private function base_query(array $statuses = [])
    {
        $query = Borrow::with(['user.image', 'book.image', 'book.category']);

        if (!empty($statuses) && !in_array('all', $statuses)) {
            $query->whereIn('status', $statuses); // ✅ whereIn بدل where
        }

        return $query;
    }

    private function has_overdue_book(int $user_id): bool
    {
        return Borrow::where('user_id', $user_id)
            ->where('status', 'overdue')
            ->exists();
    }

    private function active_borrows_count(int $user_id): int
    {
        return Borrow::where('user_id', $user_id)
            ->whereIn('status', ['pending', 'borrowed'])
            ->count();
    }

    private function is_book_available(int $book_id): bool
    {
        return Book::where('id', $book_id)
            ->where('status', 'available')
            ->where('available_copies', '>', 0)
            ->exists();
    }

    private function is_active_user(int $user_id): bool
    {
        return User::where('id', $user_id)
            ->where('status', 'active')
            ->exists();
    }

    private function already_requested(int $user_id, int $book_id): bool
    {
        return Borrow::where('user_id', $user_id)
            ->where('book_id', $book_id)
            ->whereIn('status', ['pending', 'borrowed'])
            ->exists();
    }

    public function index(array $statuses, bool $is_admin, int $user_id):array
    {
        $query = $this->base_query($statuses);

        if (!$is_admin) {
            $query->where('user_id', $user_id);
        }

        $borrows = $query->latest()->paginate(10);

        return [
            'borrows'    => $borrows->items(),
            'pagination' => [
                'current_page'  => $borrows->currentPage(),
                'last_page'     => $borrows->lastPage(),
                'per_page'      => $borrows->perPage(),
                'total'         => $borrows->total(),
                'has_more'      => $borrows->hasMorePages(),
                'next_page_url' => $borrows->nextPageUrl(),
                'prev_page_url' => $borrows->previousPageUrl(),
            ],
        ];
    }

    public function request_borrow(int $book_id, int $user_id): array
    {
        // ===================
        // Borrowing Rules
        // ===================
        if ($this->has_overdue_book($user_id)) {
            return ['status' => 'overdue'];
        }

        if ($this->active_borrows_count($user_id) >= 2) {
            return ['status' => 'max_borrows'];
        }

        if (!$this->is_book_available($book_id)) {
            return ['status' => 'unavailable'];
        }

        if ($this->already_requested($user_id, $book_id)) {
            return ['status' => 'already_requested'];
        }

        if (!$this->is_active_user($user_id)) {
            return ['status' => 'inactive_user'];
        }


        return DB::transaction(function () use ($book_id, $user_id) {

            $book = Book::lockForUpdate()->findOrFail($book_id);

            $borrow = Borrow::create([
                'user_id'      => $user_id,
                'book_id'      => $book_id,
                'status'       => 'pending',
                'requested_at' => now(),
                'expires_at'   => now()->addHours(12),
            ]);




            \App\Jobs\CancelExpiredBorrowJob::dispatch($borrow->id)
                ->delay(now()->addHours(12));

            return ['status' => 'success', 'borrow' => $borrow->load(['user', 'book.image'])];
        });
    }

    public function confirm_receive(int $borrow_id): array
    {
        return DB::transaction(function () use ($borrow_id) {

            $borrow = Borrow::lockForUpdate()->findOrFail($borrow_id);

            if ($borrow->status !== 'pending') {
                return ['status' => 'not_pending'];
            }

            $borrow->update([
                'status'      => 'borrowed',
                'borrowed_at' => now(),
                'due_at'      => now()->addDays(7),
            ]);

            $borrow->book->increment('borrowed_copies');

            \App\Jobs\CheckOverdueBorrowJob::dispatch($borrow->id)
                ->delay(now()->addDays(7));

            return ['status' => 'confirmed'];
        });
    }

    public function return_book(int $borrow_id): array
    {
        return DB::transaction(function () use ($borrow_id) {

            $borrow = Borrow::lockForUpdate()->findOrFail($borrow_id);

            if (!in_array($borrow->status, ['borrowed', 'overdue'])) {
                return ['status' => 'not_borrowed'];
            }

            $borrow->update([
                'status'      => 'returned',
                'returned_at' => now(),
            ]);

            $book = Book::lockForUpdate()->findOrFail($borrow->book_id);
            $book->decrement('borrowed_copies');
            $book->update(['status' => 'available']);

            return ['status' => 'returned'];
        });
    }
}
