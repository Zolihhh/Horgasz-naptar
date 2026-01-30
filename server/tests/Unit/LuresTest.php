<?php

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
    }
}
