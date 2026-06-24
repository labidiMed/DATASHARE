<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_file(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('rapport.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user, 'api')
            ->post('/api/v1/files', ['file' => $file, 'expires_in_days' => 3], ['Accept' => 'application/json']);

        $response->assertStatus(201)
            ->assertJsonPath('data.original_name', 'rapport.pdf')
            ->assertJsonPath('data.is_protected', false);

        $this->assertDatabaseHas('files', [
            'original_name' => 'rapport.pdf',
            'user_id' => $user->id,
        ]);
    }

    public function test_upload_requires_authentication(): void
    {
        $file = UploadedFile::fake()->create('rapport.pdf', 100);

        $this->post('/api/v1/files', ['file' => $file], ['Accept' => 'application/json'])
            ->assertStatus(401);
    }

    public function test_forbidden_extension_is_rejected(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('virus.exe', 10);

        $this->actingAs($user, 'api')
            ->post('/api/v1/files', ['file' => $file], ['Accept' => 'application/json'])
            ->assertStatus(422);
    }

    public function test_password_must_have_at_least_6_characters(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->create('doc.pdf', 10);

        $this->actingAs($user, 'api')
            ->post('/api/v1/files', ['file' => $file, 'password' => 'abc'], ['Accept' => 'application/json'])
            ->assertStatus(422);
    }

    public function test_anonymous_upload_creates_file_without_owner(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('anon.pdf', 50);

        $response = $this->post('/api/v1/files/anonymous', ['file' => $file], ['Accept' => 'application/json']);

        $response->assertStatus(201);
        $this->assertDatabaseHas('files', ['original_name' => 'anon.pdf', 'user_id' => null]);
    }
}
