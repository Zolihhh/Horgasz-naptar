<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class LocationsTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'locations';

    public static function expectedSchemaDataProvider(): array
    {
        return [
            ['id'],
            ['waterAreaCode'],
            ['latitude'],
            ['longitude'],
            ['FishingLakeName'],
        ];
    }

    public function test_locations_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable($this->table),
            "A {$this->table} tábla nem létezik"
        );
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_locations_table_has_columns(string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($this->table, $column),
            "A {$column} oszlop nem létezik"
        );
    }
}
