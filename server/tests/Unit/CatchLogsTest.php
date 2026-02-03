<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class CatchLogsTest extends TestCase
{
    use DatabaseTransactions;

    protected string $table = 'catch_logs';

    /**
     * Oszlopok listája providerből
     */
    public static function expectedSchemaDataProvider(): array
    {
        return [
            'id oszlop'            => ['id'],
            'userid oszlop'        => ['userid'],
            'locationid oszlop'    => ['locationid'],
            'comment oszlop'       => ['comment'],
            'fishing_start oszlop' => ['fishing_start'],
            'fishing_end oszlop'   => ['fishing_end'],
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
            "A '{$column}' oszlop nem létezik a '{$this->table}' táblában"
        );
    }
}
