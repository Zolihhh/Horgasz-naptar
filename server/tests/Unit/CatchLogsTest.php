<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CatchLogsTest extends TestCase
{
    use RefreshDatabase;

    protected string $table = 'catch_logs';

    public static function expectedSchemaDataProvider(): array
    {
        return [
            ['id'],
            ['userid'],
            ['locationid'],
            ['comment'],
            ['fishing_start'],
            ['fishing_end'],
        ];
    }

    public function test_catch_logs_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable($this->table),
            "A {$this->table} tábla nem létezik"
        );
    }

    #[DataProvider('expectedSchemaDataProvider')]
    public function test_catch_logs_columns_exist(string $column): void
    {
        $this->assertTrue(
            Schema::hasColumn($this->table, $column),
            "A {$column} oszlop nem létezik a {$this->table} táblában"
        );
    }
}
