<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class FileController extends Controller
{
    public function __construct(private readonly FileStorageService $storage) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $files = $request->user()->files()
            ->with('tags')
            ->when($request->filled('tag'), function ($query) use ($request) {
                $query->whereHas('tags', fn ($q) => $q->where('name', $request->input('tag')));
            })
            ->latest()
            ->paginate(15);

        return FileResource::collection($files);
    }

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

    public function show(Request $request, File $file): FileResource
    {
        $this->authorizeOwner($request, $file);

        return FileResource::make($file->load('tags'));
    }

    public function destroy(Request $request, File $file): JsonResponse
    {
        $this->authorizeOwner($request, $file);

        $this->storage->delete($file->stored_path);
        $file->delete();

        return response()->json(null, 204);
    }

    private function authorizeOwner(Request $request, File $file): void
    {
        abort_if($file->user_id !== $request->user()->id, 403, 'Action non autorisée');
    }
}
