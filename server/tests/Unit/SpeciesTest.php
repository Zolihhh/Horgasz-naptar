<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SpeciesTest extends TestCase
{
    use RefreshDatabase;


    protected string $table = 'species';

    public static function expectedSchemaDataProvider(): array
    {
        return [
            ['id'],
            ['fish_name'],
        ];
    }

    #[Test]
    public function test_species_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable($this->table),
            "A {$this->table} tábla nem létezik"
        );
    }

    #[Test]
    #[DataProvider('expectedSchemaDataProvider')]
    public function test_species_columns_exist(string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($this->table, $column),
            "A {$column} oszlop nem létezik a {$this->table} táblában"
        );
    }
}
