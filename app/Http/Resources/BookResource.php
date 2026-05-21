<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'author'           => $this->author,
            'category_name' => $this->whenLoaded(
                'category',
                fn() => $this->category->name
            ),
            'image'            => $this->whenLoaded('image', fn() =>
                $this->image
                    ? url(Storage::url($this->image->path))
                    : null
            ),
            'available_copies' => $this->available_copies,
            'borrowed_copies'  => $this->borrowed_copies,
            'status'           => $this->status,
            'created_at'       => Carbon::parse($this->created_at)->format('F j, Y'),
        ];
    }
}
