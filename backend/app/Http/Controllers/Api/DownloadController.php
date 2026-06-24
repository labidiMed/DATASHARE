<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function show(string $token): JsonResponse
    {
        $file = $this->resolve($token);

        return response()->json([
            'original_name' => $file->original_name,
            'mime_type' => $file->mime_type,
            'size_bytes' => $file->size_bytes,
            'is_protected' => $file->isProtected(),
            'expires_at' => $file->expires_at,
        ]);
    }

    public function download(Request $request, string $token): StreamedResponse
    {
        $file = $this->resolve($token);

        if ($file->isProtected()) {
            $password = $request->input('password');

            if (! $password || ! Hash::check($password, $file->password_hash)) {
                abort(401, 'Mot de passe requis ou incorrect');
            }
        }

        return Storage::download($file->stored_path, $file->original_name);
    }

    private function resolve(string $token): File
    {
        $file = File::where('download_token', $token)->first();

        abort_if($file === null, 404, 'Lien introuvable');
        abort_if($file->isExpired(), 410, 'Lien expiré');

        return $file;
    }
}
