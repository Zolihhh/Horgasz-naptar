<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class FishCatchesTest extends TestCase
{
    use DatabaseTransactions;

    protected string $table = 'fish_catches';

    /**
     * Oszlopok listája providerből (feliratozva)
     */
    public static function expectedSchemaDataProvider(): array
    {
        return [
            'id oszlop'         => ['id'],
            'specieId oszlop'   => ['specieId'],
            'lureId oszlop'     => ['lureId'],
            'catchLogId oszlop' => ['catchLogId'],
            'weight oszlop'     => ['weight'],
            'length oszlop'     => ['length'],
            'catchTime oszlop'  => ['catchTime'],
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
            "A '{$column}' oszlop nem létezik a '{$this->table}' táblában"
        );
    }
}
