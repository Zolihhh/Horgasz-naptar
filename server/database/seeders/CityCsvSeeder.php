<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CityCsvSeeder extends Seeder
{
    /**
     * Seed the application's database from CSV.
     */
    public function run(): void
    {
        $path = database_path('data/cities_hu.csv');

        if (!File::exists($path)) {
            $this->command?->warn("CSV fájl nem található: {$path}");
            return;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command?->warn("CSV fájl nem olvasható: {$path}");
            return;
        }

        $header = fgetcsv($handle, 0, ',');
        if (!$header) {
            fclose($handle);
            $this->command?->warn('A CSV fejléc hiányzik.');
            return;
        }

        // Normalize header names to avoid BOM/quote issues like "name".
        $header = array_map(static function ($value) {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value);
            return trim($value, " \t\n\r\0\x0B\"");
        }, $header);

        $rows = [];
        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if (count($data) !== count($header)) {
                continue;
            }

            $row = array_combine($header, $data);
            if (!$row || empty($row['name'])) {
                continue;
            }

            $rows[] = [
                'name' => trim((string) $row['name']),
                'county' => trim((string) ($row['county'] ?? '')) ?: null,
                'latitude' => (float) $row['latitude'],
                'longitude' => (float) $row['longitude'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        if (empty($rows)) {
            $this->command?->warn('Nincs importálható sor a cities CSV-ben.');
            return;
        }

        City::upsert(
            $rows,
            ['name', 'county', 'latitude', 'longitude'],
            ['updated_at']
        );

        $this->command?->info('Cities CSV import kész. Sorok: ' . count($rows));
    }
}
