<?php

namespace App\Jobs;

use App\Models\Borrow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckOverdueBorrowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $borrow_id) {}

    public function handle(): void
    {
        $borrow = Borrow::find($this->borrow_id);

        if (!$borrow || $borrow->status !== 'borrowed') return;

        // إذا لم يُرجع بعد 7 أيام
        $borrow->update(['status' => 'overdue']);
    }
}
