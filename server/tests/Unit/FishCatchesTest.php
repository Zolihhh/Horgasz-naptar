<?php

namespace Tests\Feature;

use App\Models\FishCatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FishCatchesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_fish_catch()
    {
        $catch = FishCatch::create([
            'speciesId'  => 1,
            'lureId'     => 2,
            'catchLogId' => 1,
            'weight'     => 6.78,
            'length'     => 15.00,
            'catchTime'  => '2026-01-28 04:27:00',
        ]);

        $this->assertDatabaseHas('fisch_catches', [
            'id'         => $catch->id,
            'speciesId'  => 1,
            'lureId'     => 2,
            'catchLogId' => 1,
            'weight'     => 6.78,
            'length'     => 15.00,
        ]);
    }

    /** @test */
    public function it_can_read_fish_catches()
    {
        FishCatch::create([
            'speciesId'  => 1,
            'lureId'     => 1,
            'catchLogId' => 1,
            'weight'     => 3.86,
            'length'     => 15.00,
            'catchTime'  => now(),
        ]);

        FishCatch::create([
            'speciesId'  => 2,
            'lureId'     => 2,
            'catchLogId' => 1,
            'weight'     => 5.09,
            'length'     => 15.00,
            'catchTime'  => now(),
        ]);

        $this->assertEquals(2, FishCatch::count());
    }

    /** @test */
    public function it_can_update_a_fish_catch()
    {
        $catch = FishCatch::create([
            'speciesId'  => 1,
            'lureId'     => 1,
            'catchLogId' => 1,
            'weight'     => 3.86,
            'length'     => 15.00,
            'catchTime'  => now(),
        ]);

        $catch->update([
            'weight' => 4.50,
            'length' => 16.20,
        ]);

        $this->assertDatabaseHas('fisch_catches', [
            'id'     => $catch->id,
            'weight' => 4.50,
            'length' => 16.20,
        ]);
    }

    /** @test */
    public function it_can_delete_a_fish_catch()
    {
        $catch = FishCatch::create([
            'speciesId'  => 1,
            'lureId'     => 1,
            'catchLogId' => 1,
            'weight'     => 3.86,
            'length'     => 15.00,
            'catchTime'  => now(),
        ]);

        $catch->delete();

        $this->assertDatabaseMissing('fisch_catches', [
            'id' => $catch->id,
        ]);
    }
}
