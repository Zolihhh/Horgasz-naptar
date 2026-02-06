<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DatabaseTest extends TestCase
{
    use DatabaseTransactions;

    /* =========================
        SEGÉD – TÍPUS NORMALIZÁLÁS
    ========================= */

    private function normalizeType(string $dbType): string
    {
        return match ($dbType) {
            'bigint'  => 'integer',
            'varchar' => 'string',
            'float'   => 'decimal', // sqlite vs mysql
            default   => $dbType,
        };
    }

    /* =========================
        TÁBLÁK
    ========================= */

    public static function tablesDataProvider(): array
    {
        return [
            ['users'],
            ['locations'],
            ['species'],
            ['lures'],
            ['catch_logs'],
            ['fish_catches'],
        ];
    }

    #[DataProvider('tablesDataProvider')]
    public function test_tables_exist(string $table): void
    {
        $this->assertTrue(
            Schema::hasTable($table),
            "A {$table} tábla nem létezik"
        );
    }

    /* =========================
        SPECIES
    ========================= */

    public static function speciesColumnsProvider(): array
    {
        return [
            ['species', 'id', 'integer'],
            ['species', 'fish_name', 'string'],
            ['species', 'photo', 'string'],
        ];
    }

    #[DataProvider('speciesColumnsProvider')]
    public function test_species_columns_types(
        string $table,
        string $column,
        string $expectedType
    ): void {
        $this->assertTrue(Schema::hasColumn($table, $column));

        $type = $this->normalizeType(
            Schema::getColumnType($table, $column)
        );

        $this->assertEquals(
            $expectedType,
            $type,
            "{$table}.{$column} típusa hibás"
        );
    }

    /* =========================
        LOCATIONS
    ========================= */

    public static function locationsColumnsProvider(): array
    {
        return [
            ['locations', 'id', 'integer'],
            ['locations', 'waterAreaCode', 'string'],
            ['locations', 'latitude', 'decimal'],
            ['locations', 'longitude', 'decimal'],
            ['locations', 'FishingLakeName', 'string'],
        ];
    }

    #[DataProvider('locationsColumnsProvider')]
    public function test_locations_columns_types(
        string $table,
        string $column,
        string $expectedType
    ): void {
        $this->assertTrue(Schema::hasColumn($table, $column));

        $type = $this->normalizeType(
            Schema::getColumnType($table, $column)
        );

        $this->assertEquals(
            $expectedType,
            $type,
            "{$table}.{$column} típusa hibás"
        );
    }

    /* =========================
        LURES
    ========================= */

    public static function luresColumnsProvider(): array
    {
        return [
            ['lures', 'id', 'integer'],
            ['lures', 'lure', 'string'],
        ];
    }

    #[DataProvider('luresColumnsProvider')]
    public function test_lures_columns_types(
        string $table,
        string $column,
        string $expectedType
    ): void {
        $this->assertTrue(Schema::hasColumn($table, $column));

        $type = $this->normalizeType(
            Schema::getColumnType($table, $column)
        );

        $this->assertEquals(
            $expectedType,
            $type,
            "{$table}.{$column} típusa hibás"
        );
    }

    /* =========================
        CATCH LOGS
    ========================= */

    public static function catchLogsColumnsProvider(): array
    {
        return [
            ['catch_logs', 'id', 'integer'],
            ['catch_logs', 'userid', 'integer'],
            ['catch_logs', 'locationid', 'integer'],
            ['catch_logs', 'comment', 'text'],
            ['catch_logs', 'fishing_start', 'date'],
            ['catch_logs', 'fishing_end', 'date'],
        ];
    }

    #[DataProvider('catchLogsColumnsProvider')]
    public function test_catch_logs_columns_types(
        string $table,
        string $column,
        string $expectedType
    ): void {
        $this->assertTrue(Schema::hasColumn($table, $column));

        $type = $this->normalizeType(
            Schema::getColumnType($table, $column)
        );

        $this->assertEquals(
            $expectedType,
            $type,
            "{$table}.{$column} típusa hibás"
        );
    }

    /* =========================
        FISH CATCHES
    ========================= */

    public static function fishCatchesColumnsProvider(): array
    {
        return [
            ['fish_catches', 'id', 'integer'],
            ['fish_catches', 'specieId', 'integer'],
            ['fish_catches', 'lureId', 'integer'],
            ['fish_catches', 'catchLogId', 'integer'],
            ['fish_catches', 'weight', 'decimal'],
            ['fish_catches', 'length', 'decimal'],
            ['fish_catches', 'catchTime', 'datetime'],
        ];
    }

    #[DataProvider('fishCatchesColumnsProvider')]
    public function test_fish_catches_columns_types(
        string $table,
        string $column,
        string $expectedType
    ): void {
        $this->assertTrue(Schema::hasColumn($table, $column));

        $type = $this->normalizeType(
            Schema::getColumnType($table, $column)
        );

        $this->assertEquals(
            $expectedType,
            $type,
            "{$table}.{$column} típusa hibás"
        );
    }
}
