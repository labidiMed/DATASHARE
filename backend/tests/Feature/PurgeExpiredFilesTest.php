<?php

namespace Tests\Feature;

use App\Models\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurgeExpiredFilesTest extends TestCase
{
    use RefreshDatabase;

    public function test_purge_command_deletes_only_expired_files(): void
    {
        Storage::fake('local');
        $active = File::factory()->create();
        $expired = File::factory()->expired()->create();

        $this->artisan('files:purge')->assertSuccessful();

        $this->assertDatabaseHas('files', ['id' => $active->id]);
        $this->assertDatabaseMissing('files', ['id' => $expired->id]);
    }
}
