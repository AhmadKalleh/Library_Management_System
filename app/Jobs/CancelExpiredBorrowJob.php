<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CancelExpiredBorrowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $borrow_id) {}

    public function handle(): void
    {
        DB::transaction(function () {
            $borrow = Borrow::lockForUpdate()->find($this->borrow_id);

            // نتحقق إذا لا زال pending
            if (!$borrow || $borrow->status !== 'pending') return;

            $borrow->update(['status' => 'cancelled']);

            // إعادة النسخة للكتاب
            $book = Book::lockForUpdate()->find($borrow->book_id);
            $book->increment('available_copies');
            $book->update(['status' => 'available']);
        });
    }
}
