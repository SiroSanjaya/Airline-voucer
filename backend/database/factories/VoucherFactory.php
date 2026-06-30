<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'crew_name'     => $this->faker->name(),
            'crew_id'       => $this->faker->numerify('####'),
            'flight_number' => strtoupper($this->faker->lexify('??') . $this->faker->numerify('###')),
            'flight_date'   => $this->faker->date('Y-m-d'),
            'aircraft_type' => $this->faker->randomElement(['ATR', 'Airbus 320', 'Boeing 737 Max']),
            'seat1'         => '1A',
            'seat2'         => '2B',
            'seat3'         => '3C',
        ];
    }
}
