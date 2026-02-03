<?php

namespace Tests\Feature;

use App\Models\Lure;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LuresTest extends TestCase
{
    use DatabaseTransactions;

    protected string $table = 'lures';
    

    public function test_lure_column_unique(): void
    {
        $name = "Lure1";
        try {
            DB::table('lures')->insert(['lure' => $name, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('lures')->insert(['lure' => $name, 'created_at' => now(), 'updated_at' => now()]);
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1]; // MySQL hibakód
        }

        // ellenőrizzük, hogy valóban duplicate entry hibát kaptunk
        $this->assertEquals(
            1062,
            $errorCode,
            'Duplicate entry error code (1062) expected for unique constraint violation.'
        );

    }


    public function test_it_can_update_a_lure(): void
    {
        // Véletlenszerű név generálása a teszthez
        $original = 'OriginalLure';
        $updated = 'UpdatedLure';

        $lure = Lure::create(['lure' => $original]);

        $lure->update([
            'lure' => $updated,
        ]);

        $this->assertDatabaseHas($this->table, [
            'id' => $lure->id,
            'lure' => $updated,
        ]);
    }


    public function test_it_can_delete_a_lure(): void
    {
        $lureName = 'DeleteMe';
        $lure = Lure::create(['lure' => $lureName]);

        $lure->delete();

        $this->assertDatabaseMissing($this->table, [
            'id' => $lure->id,
        ]);
    }


    public function test_lures_table_exists(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasTable($this->table),
            "A {$this->table} tábla nem létezik"
        );
    }

    public function test_lure_column_exists(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Schema::hasColumn($this->table, 'lure'),
            "A 'lure' oszlop nem létezik a {$this->table} táblában"
        );
    }
}
