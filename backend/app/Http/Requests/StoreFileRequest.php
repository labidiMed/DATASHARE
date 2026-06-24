<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreFileRequest extends FormRequest
{
    /**
     * Extensions interdites pour raisons de sécurité (exécutables / scripts).
     */
    private const FORBIDDEN_EXTENSIONS = [
        'exe', 'bat', 'cmd', 'com', 'msi', 'scr', 'sh', 'bash',
        'ps1', 'vbs', 'js', 'jar', 'app', 'dll', 'deb', 'apk',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:1048576', $this->safeExtensionRule()], // 1 Go en Ko
            'expires_in_days' => ['nullable', 'integer', 'min:1', 'max:7'],
            'password' => ['nullable', 'string', 'min:6'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:30'],
        ];
    }

    private function safeExtensionRule(): Closure
    {
        return function (string $attribute, $value, Closure $fail) {
            if ($value instanceof UploadedFile) {
                $extension = strtolower($value->getClientOriginalExtension());
                if (in_array($extension, self::FORBIDDEN_EXTENSIONS, true)) {
                    $fail("Le type de fichier .{$extension} n'est pas autorisé pour des raisons de sécurité.");
                }
            }
        };
    }
}
