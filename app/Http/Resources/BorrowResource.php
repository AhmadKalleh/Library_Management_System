<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BookResource;

class BorrowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'user' => $this->whenLoaded('user', fn() => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
                'image' => $this->user->relationLoaded('image') && $this->user->image
                    ? url(Storage::url($this->user->image->path))
                    : url(Storage::url('users/profile-user.png')), // صورة افتراضية
            ]),
            'book' => $this->whenLoaded('book', function () {
                return [
                    'id'     => $this->book->id,
                    'title'  => $this->book->title,
                    'author' => $this->book->author,
                    'image'  => $this->book->relationLoaded('image') && $this->book->image
                        ? url(Storage::url($this->book->image->path))
                        : null,
                ];
            }),
            'requested_at' => Carbon::parse($this->requested_at)->format('F j, Y'),
            'expires_at'   => Carbon::parse($this->expires_at)->format('F j, Y'),
            'borrowed_at'  => Carbon::parse($this->borrowed_at)->format('F j, Y'),
            'due_at'       => Carbon::parse($this->due_at)->format('F j, Y'),
            'returned_at'  => Carbon::parse($this->returned_at)->format('F j, Y'),
            'time_remaining' => $this->when(
                in_array($this->status, ['borrowed', 'overdue']),
                fn() => $this->due_at
                    ? now()->diffForHumans($this->due_at, [
                        'parts' => 2,
                        'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                    ])
                    : null
            ),
        ];
    }
}
