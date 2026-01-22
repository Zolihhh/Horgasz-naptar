<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Model;
use App\Models\CatchLog;
use App\Models\Specie;
use App\Models\Lure;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FishCatch>
 */
class FishCatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'specieId'=> Specie::inRandomOrder()->first()->id,
            'weight'=>round(rand(0, 1000) / 100, 2),
            'length'=>round(15, 300),
            'lureId'=>Lure::inRandomOrder()->first()->id,
            'catchTime'=>$this->faker->dateTimeBetween('-2days', 'now'),
            'catchLogId'=>CatchLog::inRandomOrder()->first()->id,
        ];
    }
}
