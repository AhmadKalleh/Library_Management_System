<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'mobile'  => $this->mobile,
            'status'  => $this->status,
            'image'   => url(Storage::url(
                $this->image->path ?? 'users/profile-user.png'
            )),
            'currently_borrowed_books' => $this->whenLoaded('borrows', fn() =>
                $this->borrows
                    ->whereIn('status', ['borrowed', 'overdue'])
                    ->map(fn($borrow) => [
                        'book_name'      => $borrow->book?->title,
                        'borrow_date'    => Carbon::parse($borrow->borrowed_at)->format('d M, Y'),
                        'due_date'       => Carbon::parse($borrow->due_at)->format('d M, Y'),
                        'time_remaining' => $this->get_time_remaining($borrow),
                    ])->values()
            ),
        ];
    }


    private function get_time_remaining($borrow): array
    {
        if ($borrow->status === 'overdue') {
            $days_overdue = now()->diffInDays($borrow->due_at);
            return [
                'status' => 'overdue',
                'label'  => "Overdue · {$days_overdue} Days",
            ];
        }

        $days_left = now()->diffInDays($borrow->due_at, false);
        return [
            'status' => 'on_time',
            'label'  => "On Time · {$days_left} Days Left",
        ];
    }
}
