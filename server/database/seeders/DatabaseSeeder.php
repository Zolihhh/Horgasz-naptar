<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //Mielőtt seedelünk, minden táblát töröljünk le.
        DB::statement('DELETE FROM users');
        DB::statement('DELETE FROM catch_logs');
        DB::statement('DELETE FROM fish_catches');
        DB::statement('DELETE FROM locations');
        DB::statement('DELETE FROM lures');
        DB::statement('DELETE FROM species');



        //Ami Seeder osztály itt fel van sorolva, annak lefut a run() metódusa
        $this->call([
            UserSeeder::class,
            LureSeeder::class,
            SpecieSeeder::class,
            LocationSeeder::class,
            CatchLogSeeder::class,
            //FishCatchSeeder::class,
        ]);
    }
}
