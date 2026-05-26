<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['pending', 'borrowed', 'returned', 'cancelled', 'overdue'];

        $data = [];

        foreach (range(3, 5) as $userId) {
            foreach (range(1, 12) as $bookId) {

                $status = $statuses[array_rand($statuses)];

                $requestedAt = Carbon::now()->subDays(rand(0, 10));
                $expiresAt = (clone $requestedAt)->addHours(12);

                $borrowedAt = null;
                $dueAt = null;
                $returnedAt = null;

                if ($status === 'borrowed' || $status === 'returned' || $status === 'overdue') {
                    $borrowedAt = (clone $requestedAt)->addHours(rand(1, 5));
                    $dueAt = (clone $borrowedAt)->addDays(7);
                }

                if ($status === 'returned') {
                    $returnedAt = (clone $borrowedAt)->addDays(rand(1, 6));
                }

                if ($status === 'overdue') {
                    $dueAt = Carbon::now()->subDays(rand(1, 3)); // انتهى ولم يرجع
                }

                if ($status === 'cancelled') {
                    $borrowedAt = null;
                    $dueAt = null;
                }

                $data[] = [
                    'user_id' => $userId,
                    'book_id' => $bookId,
                    'status' => $status,
                    'requested_at' => $requestedAt,
                    'expires_at' => $expiresAt,
                    'borrowed_at' => $borrowedAt,
                    'due_at' => $dueAt,
                    'returned_at' => $returnedAt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('borrows')->insert($data);
    }
}
