<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Location;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CatchLog>
 */
class CatchLogFactory extends Factory
{
    protected function withFaker()
    {
        return \Faker\Factory::create('hu_HU');
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userid'=> User::inRandomOrder()->first()->id,
            'locationId'=> Location::inRandomOrder()->first()->id,
            'comment'=> "Jó hal",
            'fishing_start' => $this->faker->dateTimeBetween('-2 days', 'now'),
            'fishing_end'=> now(),
        ];
    }
}
