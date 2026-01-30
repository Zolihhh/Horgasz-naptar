<?php

<<<<<<< HEAD
namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class LuresTest extends TestCase
{
    protected $table = 'lures';

    public static function expectedSchemaDataProvider()
    {
        return [
            ['id', ['bigint', 'integer']],
            ['lure', 'varchar'],
        ];
    }

    public function test_lures_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_lures_columns_exist(string $column): void
    {
        $this->assertTrue(Schema::hasColumn($this->table, $column));
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_lures_column_types(string $column, $expectedType): void
    {
        $actualType = Schema::getColumnType($this->table, $column);

        if (is_array($expectedType)) {
            $this->assertTrue(in_array($actualType, $expectedType));
        } else {
            $this->assertEquals($expectedType, $actualType);
        }
=======
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
>>>>>>> 2b61c8e03fa8a3fccd669c6b9412da37df5c2efb
    }
}
