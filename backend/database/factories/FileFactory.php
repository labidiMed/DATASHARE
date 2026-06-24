<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FileFactory extends Factory
{
    protected $model = File::class;

    public function definition(): array
    {
        return [
            'original_name' => fake()->word().'.pdf',
            'stored_path' => 'files/'.Str::random(20).'.pdf',
            'mime_type' => 'application/pdf',
            'size_bytes' => fake()->numberBetween(100, 100000),
            'download_token' => Str::random(40),
            'password_hash' => null,
            'expires_at' => now()->addDays(7),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expires_at' => now()->subDay()]);
    }

    public function protectedWith(string $password): static
    {
        return $this->state(fn () => ['password_hash' => Hash::make($password)]);
    }
}
