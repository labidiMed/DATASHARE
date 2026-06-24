<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class FileController extends Controller
{
    public function __construct(private readonly FileStorageService $storage) {}

    public function store(StoreFileRequest $request): JsonResponse
    {
        $upload = $request->file('file');

        $file = $request->user()->files()->create([
            'original_name' => $upload->getClientOriginalName(),
            'stored_path' => $this->storage->store($upload),
            'mime_type' => $upload->getClientMimeType(),
            'size_bytes' => $upload->getSize(),
            'download_token' => $this->storage->generateToken(),
            'password_hash' => $request->filled('password') ? Hash::make($request->input('password')) : null,
            'expires_at' => now()->addDays($request->integer('expires_in_days', 7)),
        ]);

        if ($request->filled('tags')) {
            $tagIds = collect($request->input('tags'))
                ->unique()
                ->map(fn (string $name) => $request->user()->tags()->firstOrCreate(['name' => $name])->id);

            $file->tags()->sync($tagIds);
        }

        return FileResource::make($file->load('tags'))
            ->response()
            ->setStatusCode(201);
    }
}
