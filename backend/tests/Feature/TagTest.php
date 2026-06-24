<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_with_tags_creates_and_lists_them(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 50);

        $this->actingAs($user, 'api')->post('/api/v1/files', [
            'file' => $file,
            'tags' => ['factures', '2026'],
        ], ['Accept' => 'application/json'])->assertStatus(201);

        $this->actingAs($user, 'api')
            ->getJson('/api/v1/tags')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['name' => 'factures']);
    }

    public function test_history_can_be_filtered_by_tag(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user, 'api')->post('/api/v1/files', [
            'file' => UploadedFile::fake()->create('a.pdf', 10),
            'tags' => ['factures'],
        ], ['Accept' => 'application/json']);

        $this->actingAs($user, 'api')->post('/api/v1/files', [
            'file' => UploadedFile::fake()->create('b.pdf', 10),
        ], ['Accept' => 'application/json']);

        $this->actingAs($user, 'api')
            ->getJson('/api/v1/files?tag=factures')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
