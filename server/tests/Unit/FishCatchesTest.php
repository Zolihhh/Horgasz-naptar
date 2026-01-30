<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class FishCatchesTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'fish_catches';

    public static function expectedSchemaDataProvider(): array
    {
        return [
            ['id'],
            ['specieId'],
            ['lureId'],
            ['catchLogId'],
            ['weight'],
            ['length'],
            ['catchTime'],
        ];
    }

    public function test_fish_catches_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable($this->table),
            "A {$this->table} tábla nem létezik"
        );
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_fish_catches_columns_exist(string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($this->table, $column),
            "A {$column} oszlop nem létezik a {$this->table} táblában"
        );
    }
}
