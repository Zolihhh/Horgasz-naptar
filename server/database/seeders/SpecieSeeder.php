<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;
use App\Models\Specie;

class SpecieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // CSV fájl elérési útja
        $fileName = 'csv/species.csv';
        $delimiter = ';'; // ha a CSV pontosvesszővel van elválasztva

        // CSV betöltése tömbbe
        $data = CsvReader::csvToArray($fileName, $delimiter);

        // Csak akkor töltjük fel, ha nincs még adat a táblában
        if (Specie::count() === 0) {
            foreach ($data as &$row) {
                // Ellenőrizzük, hogy van-e photo oszlop, ha nincs akkor null
                $row = [
                    'id' => $row['id'] ?? null,
                    'photo' => $row['photo'] ?? null,
                    'fish_name' => $row['fish_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Specie::insert($data);
        }
    }
}
