<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    
    protected $model = Event::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'type' => $this->faker->randomElement(['FLT', 'DO', 'SBY']),
            'check_in' => $this->faker->dateTime(),
            'check_out' => $this->faker->dateTime(),
            'flight_number' => $this->faker->optional()->regexify('[A-Z]{2}[0-9]{3}'),
            'start_time' => $this->faker->dateTime(),
            'end_time' => $this->faker->dateTime(),
            'start_location' => $this->faker->lexify('???'),
            'end_location' => $this->faker->lexify('???'),
        ];
    }
}
