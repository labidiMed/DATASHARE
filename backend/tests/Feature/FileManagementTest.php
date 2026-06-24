<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_only_their_own_files(): void
    {
        $user = User::factory()->create();
        File::factory()->count(2)->create(['user_id' => $user->id]);
        File::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->actingAs($user, 'api')
            ->getJson('/api/v1/files')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_user_cannot_view_another_users_file(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other, 'api')
            ->getJson("/api/v1/files/{$file->id}")
            ->assertStatus(403);
    }

    public function test_user_can_delete_own_file(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $file = File::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'api')
            ->deleteJson("/api/v1/files/{$file->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('files', ['id' => $file->id]);
    }

    public function test_user_cannot_delete_another_users_file(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $file = File::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other, 'api')
            ->deleteJson("/api/v1/files/{$file->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('files', ['id' => $file->id]);
    }
}
