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
        Specie::factory()->createMany($data);
        
        }
        
    }
    

