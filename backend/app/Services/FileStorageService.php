<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    public function store(UploadedFile $file): string
    {
        return $file->store('files');
    }

    public function delete(string $path): void
    {
        Storage::delete($path);
    }

    public function generateToken(): string
    {
        do {
            $token = Str::random(40);
        } while (File::where('download_token', $token)->exists());

        return $token;
    }
}
