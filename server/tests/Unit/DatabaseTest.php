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
        TÁBLÁK
    ========================= */

    public static function tablesDataProvider(): array
    {
        return [
            'users tábla'        => ['users'],
            'locations tábla'    => ['locations'],
            'species tábla'      => ['species'],
            'lures tábla'        => ['lures'],
            'catch_logs tábla'   => ['catch_logs'],
            'fish_catches tábla' => ['fish_catches'],
        ];
    }

    #[DataProvider('tablesDataProvider')]
    public function test_tables_exist(string $table): void
    {
        $this->assertTrue(
            Schema::hasTable($table),
            "A '{$table}' tábla nem létezik"
        );
    }

    /* =========================
        SPECIES
    ========================= */

    public static function speciesColumnsProvider(): array
    {
        return [
            'id oszlop'        => ['species', 'id'],
            'fish_name oszlop' => ['species', 'fish_name'],
            'photo oszlop'     => ['species', 'photo'],
        ];
    }

    #[DataProvider('speciesColumnsProvider')]
    public function test_species_columns_exist(string $table, string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($table, $column),
            "A '{$column}' oszlop nem létezik a '{$table}' táblában"
        );
    }

    /* =========================
        LOCATIONS
    ========================= */

    public static function locationsColumnsProvider(): array
    {
        return [
            ['locations', 'id'],
            ['locations', 'waterAreaCode'],
            ['locations', 'latitude'],
            ['locations', 'longitude'],
            ['locations', 'FishingLakeName'],
        ];
    }

    #[DataProvider('locationsColumnsProvider')]
    public function test_locations_columns_exist(string $table, string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($table, $column),
            "A '{$column}' oszlop nem létezik a '{$table}' táblában"
        );
    }

    /* =========================
        LURES
    ========================= */

    public static function luresColumnsProvider(): array
    {
        return [
            ['lures', 'id'],
            ['lures', 'lure'],
        ];
    }

    #[DataProvider('luresColumnsProvider')]
    public function test_lures_columns_exist(string $table, string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($table, $column),
            "A '{$column}' oszlop nem létezik a '{$table}' táblában"
        );
    }

    /* =========================
        CATCH LOGS
    ========================= */

    public static function catchLogsColumnsProvider(): array
    {
        return [
            ['catch_logs', 'id'],
            ['catch_logs', 'userid'],
            ['catch_logs', 'locationid'],
            ['catch_logs', 'comment'],
            ['catch_logs', 'fishing_start'],
            ['catch_logs', 'fishing_end'],
        ];
    }

    #[DataProvider('catchLogsColumnsProvider')]
    public function test_catch_logs_columns_exist(string $table, string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($table, $column),
            "A '{$column}' oszlop nem létezik a '{$table}' táblában"
        );
    }

    /* =========================
        FISH CATCHES
    ========================= */

    public static function fishCatchesColumnsProvider(): array
    {
        return [
            ['fish_catches', 'id'],
            ['fish_catches', 'specieId'],
            ['fish_catches', 'lureId'],
            ['fish_catches', 'catchLogId'],
            ['fish_catches', 'weight'],
            ['fish_catches', 'length'],
            ['fish_catches', 'catchTime'],
        ];
    }

    #[DataProvider('fishCatchesColumnsProvider')]
    public function test_fish_catches_columns_exist(string $table, string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($table, $column),
            "A '{$column}' oszlop nem létezik a '{$table}' táblában"
        );
    }
}
