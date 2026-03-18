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
        Schema::table('species', function (Blueprint $table) {
            $table->string('description')->nullable()->after('fish_name');
        });

        $this->fillExistingDescriptionsFromCsv();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('species', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }

    private function fillExistingDescriptionsFromCsv(): void
    {
        $filePath = database_path('csv/species.csv');

        if (!is_file($filePath)) {
            return;
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return;
        }

        $header = fgetcsv($handle, 0, ';');

        while (($columns = fgetcsv($handle, 0, ';')) !== false) {
            if (!$header || count($header) !== count($columns)) {
                continue;
            }

            $row = array_combine($header, $columns);
            if (!$row || empty($row['fish_name']) || empty($row['description'])) {
                continue;
            }

            DB::table('species')
                ->where('fish_name', $row['fish_name'])
                ->update(['description' => $row['description']]);
        }

        fclose($handle);
    }
};
