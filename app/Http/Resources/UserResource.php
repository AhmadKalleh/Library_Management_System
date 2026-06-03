<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'mobile'      => $this->mobile,
            'role'        => $this->role,
            'status'      => $this->status,
            'is_verified' => !is_null($this->email_verified_at),
            'image' => url(Storage::url(
                $this->image->path ?? 'users/profile-user.png'
            )),
            'created_at' => Carbon::parse($this->created_at)->format('F j, Y'),
        ];
    }
}
