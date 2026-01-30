<?php

namespace Tests\Feature;

use App\Models\Lure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LuresTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'lures';

    // Adat szolgáltató a tesztekhez
    public static function lureDataProvider(): array
    {
        return [
            ['Lure1'],
            ['Lure2'],
            ['Lure3'],
            ['Lure4'],
            ['Lure5'],
        ];
    }

    #[Test]
    #[DataProvider('lureDataProvider')]
    public function it_can_create_a_lure(string $lureName): void
    {
        $lure = Lure::create([
            'lure' => $lureName,
        ]);

        $this->assertDatabaseHas($this->table, [
            'lure' => $lureName,
        ]);
    }

    #[Test]
    public function it_can_read_lures(): void
    {
        foreach (self::lureDataProvider() as [$name]) {
            Lure::create(['lure' => $name]);
        }

        $this->assertEquals(count(self::lureDataProvider()), Lure::count());
    }

    #[Test]
    public function it_can_update_a_lure(): void
    {
        // Véletlenszerű név generálása a teszthez
        $original = 'OriginalLure';
        $updated  = 'UpdatedLure';

        $lure = Lure::create(['lure' => $original]);

        $lure->update([
            'lure' => $updated,
        ]);

        $this->assertDatabaseHas($this->table, [
            'id'   => $lure->id,
            'lure' => $updated,
        ]);
    }

    #[Test]
    public function it_can_delete_a_lure(): void
    {
        $lureName = 'DeleteMe';
        $lure = Lure::create(['lure' => $lureName]);

        $lure->delete();

        $this->assertDatabaseMissing($this->table, [
            'id' => $lure->id,
        ]);
    }

    #[Test]
    public function test_lures_table_exists(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasTable($this->table),
            "A {$this->table} tábla nem létezik"
        );
    }

    #[Test]
    public function test_lure_column_exists(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasColumn($this->table, 'lure'),
            "A 'lure' oszlop nem létezik a {$this->table} táblában"
        );
    }
}
