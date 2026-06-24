<?php

namespace Tests\Feature;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_metadata_is_available(): void
    {
        $file = File::factory()->create(['original_name' => 'public.pdf']);

        $this->getJson("/api/v1/download/{$file->download_token}")
            ->assertOk()
            ->assertJsonPath('original_name', 'public.pdf')
            ->assertJsonPath('is_protected', false);
    }

    public function test_invalid_token_returns_404(): void
    {
        $this->getJson('/api/v1/download/inexistant')->assertStatus(404);
    }

    public function test_expired_link_returns_410(): void
    {
        $file = File::factory()->expired()->create();

        $this->getJson("/api/v1/download/{$file->download_token}")->assertStatus(410);
    }

    public function test_protected_file_requires_password(): void
    {
        $file = File::factory()->protectedWith('secret123')->create();

        $this->postJson("/api/v1/download/{$file->download_token}", [])->assertStatus(401);
        $this->postJson("/api/v1/download/{$file->download_token}", ['password' => 'mauvais'])->assertStatus(401);
    }

    public function test_file_can_be_downloaded(): void
    {
        Storage::fake('local');
        $file = File::factory()->create(['stored_path' => 'files/test.pdf']);
        Storage::disk('local')->put('files/test.pdf', 'contenu du fichier');

        $this->post("/api/v1/download/{$file->download_token}")->assertOk();
    }
}
