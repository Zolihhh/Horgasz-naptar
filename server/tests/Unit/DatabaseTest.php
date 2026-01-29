<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_tables_exist()
    {
        foreach ([
            'users',
            'locations',
            'species',
            'lures',
            'catch_logs',
            'fish_catches',
        ] as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "{$table} tábla nem létezik"
            );
        }
    }

    public function test_species_table_structure()
    {
        $this->assertTrue(Schema::hasTable('species'));

        $this->assertTrue(Schema::hasColumn('species', 'id'));
        $this->assertTrue(Schema::hasColumn('species', 'fish_name'));
        $this->assertTrue(Schema::hasColumn('species', 'photo'));

        // SQLite vs MySQL kompatibilitás
        $idType = Schema::getColumnType('species', 'id');
        $this->assertTrue(
            in_array($idType, ['bigint', 'integer']),
            "species.id típusa nem megfelelő: {$idType}"
        );

        $this->assertEquals(
            'varchar',
            Schema::getColumnType('species', 'fish_name')
        );
    }

    public function test_locations_table_structure()
    {
        $this->assertTrue(Schema::hasColumn('locations', 'id'));
        $this->assertTrue(Schema::hasColumn('locations', 'waterAreaCode'));
        $this->assertTrue(Schema::hasColumn('locations', 'latitude'));
        $this->assertTrue(Schema::hasColumn('locations', 'longitude'));
        $this->assertTrue(Schema::hasColumn('locations', 'FishingLakeName'));

        $idType = Schema::getColumnType('locations', 'id');
        $this->assertTrue(
            in_array($idType, ['bigint', 'integer']),
            "locations.id típusa nem megfelelő: {$idType}"
        );
    }

    public function test_lures_table_structure()
    {
        $this->assertTrue(Schema::hasColumn('lures', 'id'));
        $this->assertTrue(Schema::hasColumn('lures', 'lure'));

        $idType = Schema::getColumnType('lures', 'id');
        $this->assertTrue(
            in_array($idType, ['bigint', 'integer']),
            "lures.id típusa nem megfelelő: {$idType}"
        );

        $this->assertEquals(
            'varchar',
            Schema::getColumnType('lures', 'lure')
        );
    }

    public function test_catch_logs_foreign_key_columns_exist()
    {
        // migráció szerint: userid, locationid
        $this->assertTrue(Schema::hasColumn('catch_logs', 'userid'));
        $this->assertTrue(Schema::hasColumn('catch_logs', 'locationid'));
        $this->assertTrue(Schema::hasColumn('catch_logs', 'fishing_start'));
        $this->assertTrue(Schema::hasColumn('catch_logs', 'fishing_end'));
    }

    public function test_fish_catches_foreign_key_columns_exist()
    {
        // migráció szerint: specieId, lureId, catchLogId
        $this->assertTrue(Schema::hasColumn('fish_catches', 'specieId'));
        $this->assertTrue(Schema::hasColumn('fish_catches', 'lureId'));
        $this->assertTrue(Schema::hasColumn('fish_catches', 'catchLogId'));

        $this->assertTrue(Schema::hasColumn('fish_catches', 'weight'));
        $this->assertTrue(Schema::hasColumn('fish_catches', 'length'));
        $this->assertTrue(Schema::hasColumn('fish_catches', 'catchTime'));
    }
}
