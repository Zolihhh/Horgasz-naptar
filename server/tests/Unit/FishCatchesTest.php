<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class FishCatchesTest extends TestCase
{
    protected $table = 'fish_catches';

    public static function expectedSchemaDataProvider()
    {
        return [
            ['id', ['bigint', 'integer']],
            ['specieId', ['bigint', 'integer']],
            ['lureId', ['bigint', 'integer']],
            ['catchLogId', ['bigint', 'integer']],
            ['weight', 'decimal'],
            ['length', 'decimal'],
            ['catchTime', 'datetime'],
        ];
    }

    public function test_fish_catches_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_fish_catches_columns_exist(string $column): void
    {
        $this->assertTrue(Schema::hasColumn($this->table, $column));
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_fish_catches_column_types(string $column, $expectedType): void
    {
        $actualType = Schema::getColumnType($this->table, $column);

        if (is_array($expectedType)) {
            $this->assertTrue(in_array($actualType, $expectedType));
        } else {
            $this->assertEquals($expectedType, $actualType);
        }
    }
}
