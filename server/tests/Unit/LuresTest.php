<?php

namespace Tests\Feature;

use App\Models\Lure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LuresTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_lure(): void
    {
        $lure = Lure::create([
            'lure' => 'Kukorica',
        ]);

        $this->assertDatabaseHas('lures', [
            'lure' => 'Kukorica',
        ]);
    }

    #[Test]
    public function it_can_read_lures(): void
    {
        Lure::create(['lure' => 'Kukorica']);
        Lure::create(['lure' => 'Csemegekukorica']);

        $this->assertEquals(2, Lure::count());
    }

    #[Test]
    public function it_can_update_a_lure(): void
    {
        $lure = Lure::create(['lure' => 'Főtt kukorica']);

        $lure->update([
            'lure' => 'Tigrismogyoró',
        ]);

        $this->assertDatabaseHas('lures', [
            'id'   => $lure->id,
            'lure' => 'Tigrismogyoró',
        ]);
    }

    #[Test]
    public function it_can_delete_a_lure(): void
    {
        $lure = Lure::create(['lure' => 'Boji']);

        $lure->delete();

        $this->assertDatabaseMissing('lures', [
            'id' => $lure->id,
        ]);
    }
}
