<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable([
    'user_id',
    'original_name',
    'stored_path',
    'mime_type',
    'size_bytes',
    'download_token',
    'password_hash',
    'expires_at',
])]
#[Hidden(['stored_path', 'password_hash'])]
class File extends Model
{
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'size_bytes' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function isProtected(): bool
    {
        return $this->password_hash !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
