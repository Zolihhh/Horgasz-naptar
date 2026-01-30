<?php

namespace Tests\Feature;

use App\Models\Lure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LuresTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_lure()
    {
        $lure = Lure::create([
            'lure' => 'Saját csali',
        ]);

        $this->assertDatabaseHas('lures', [
            'id'   => $lure->id,
            'lure' => 'Saját csali',
        ]);
    }

    /** @test */
    public function it_can_read_lures()
    {
        Lure::create(['lure' => 'Kukorica']);
        Lure::create(['lure' => 'Csemegekukorica']);

        $this->assertEquals(2, Lure::count());
    }

    /** @test */
    public function it_can_update_a_lure()
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

    /** @test */
    public function it_can_delete_a_lure()
    {
        $lure = Lure::create(['lure' => 'Boji']);

        $lure->delete();

        $this->assertDatabaseMissing('lures', [
            'id' => $lure->id,
        ]);
    }
}
