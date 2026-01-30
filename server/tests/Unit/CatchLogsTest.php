<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CatchLogsTest extends TestCase
{
    protected $table = 'catch_logs';

    public static function expectedSchemaDataProvider()
    {
        return [
            ['id', ['bigint', 'integer']],
            ['userid', ['bigint', 'integer']],
            ['locationid', ['bigint', 'integer']],
            ['comment', 'text'],
            ['fishing_start', 'date'],
            ['fishing_end', 'date'],
        ];
    }

    public function test_catch_logs_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable($this->table));
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_catch_logs_columns_exist(string $column): void
    {
        $this->assertTrue(Schema::hasColumn($this->table, $column));
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_catch_logs_column_types(string $column, $expectedType): void
    {
        $actualType = Schema::getColumnType($this->table, $column);

        if (is_array($expectedType)) {
            $this->assertTrue(in_array($actualType, $expectedType));
        } else {
            $this->assertEquals($expectedType, $actualType);
        }
    }
}
