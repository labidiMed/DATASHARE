<?php

namespace Tests\Unit;

use App\Models\File;
use Tests\TestCase;

class FileModelTest extends TestCase
{
    public function test_is_expired_returns_true_for_past_date(): void
    {
        $file = new File(['expires_at' => now()->subDay()]);

        $this->assertTrue($file->isExpired());
    }

    public function test_is_expired_returns_false_for_future_date(): void
    {
        $file = new File(['expires_at' => now()->addDay()]);

        $this->assertFalse($file->isExpired());
    }

    public function test_is_protected_depends_on_password_hash(): void
    {
        $protected = new File(['password_hash' => 'hash']);
        $open = new File(['password_hash' => null]);

        $this->assertTrue($protected->isProtected());
        $this->assertFalse($open->isProtected());
    }
}
