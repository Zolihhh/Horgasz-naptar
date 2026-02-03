<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class LocationsTest extends TestCase
{
    use DatabaseTransactions;

    protected string $table = 'locations';

    /**
     * Oszlopok listája providerből (feliratozva)
     */
    public static function expectedSchemaDataProvider(): array
    {
        return [
            'id oszlop'             => ['id'],
            'waterAreaCode oszlop'  => ['waterAreaCode'],
            'latitude oszlop'       => ['latitude'],
            'longitude oszlop'      => ['longitude'],
            'FishingLakeName oszlop'=> ['FishingLakeName'],
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
            "A '{$column}' oszlop nem létezik a '{$this->table}' táblában"
        );
    }
}
