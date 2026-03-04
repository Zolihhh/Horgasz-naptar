<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('cities')) {
            return;
        }

        foreach ([
            'cities_name_postal_code_country_code_unique',
            'cities_name_postal_code_unique',
        ] as $indexName) {
            if ($this->indexExists('cities', $indexName)) {
                DB::statement("ALTER TABLE `cities` DROP INDEX `{$indexName}`");
            }
        }

        if (Schema::hasColumn('cities', 'country_code')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropColumn('country_code');
            });
        }

        if (Schema::hasColumn('cities', 'postal_code')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropColumn('postal_code');
            });
        }

        if (!$this->indexExists('cities', 'cities_name_county_lat_lng_unique')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->unique(['name', 'county', 'latitude', 'longitude'], 'cities_name_county_lat_lng_unique');
            });
        }

        if (!$this->indexExists('cities', 'cities_name_index')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->index(['name']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('cities')) {
            return;
        }

        if ($this->indexExists('cities', 'cities_name_county_lat_lng_unique')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropUnique('cities_name_county_lat_lng_unique');
            });
        }

        if (!Schema::hasColumn('cities', 'postal_code')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->string('postal_code', 12)->nullable()->after('county');
            });
        }

        if (!Schema::hasColumn('cities', 'country_code')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->string('country_code', 2)->default('HU')->after('postal_code');
            });
        }

        if (!$this->indexExists('cities', 'cities_name_postal_code_country_code_unique')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->unique(['name', 'postal_code', 'country_code']);
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        $result = DB::selectOne(
            'SELECT COUNT(1) AS cnt
             FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $index]
        );

        return (int) ($result->cnt ?? 0) > 0;
    }
};
