<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Services\FileStorageService;
use Illuminate\Console\Command;

class PurgeExpiredFiles extends Command
{
    protected $signature = 'files:purge';

    protected $description = 'Supprime les fichiers expirés et leurs métadonnées (US10)';

    public function handle(FileStorageService $storage): int
    {
        $expired = File::where('expires_at', '<', now())->get();

        foreach ($expired as $file) {
            $storage->delete($file->stored_path);
            $file->delete();
        }

        $this->info("Fichiers expirés purgés : {$expired->count()}");

        return self::SUCCESS;
    }
}
