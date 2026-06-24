<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size_bytes' => $this->size_bytes,
            'download_token' => $this->download_token,
            'download_url' => url('/api/v1/download/'.$this->download_token),
            'is_protected' => $this->isProtected(),
            'expires_at' => $this->expires_at,
            'is_expired' => $this->isExpired(),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at,
        ];
    }
}
