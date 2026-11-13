<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Attendee>
 */
class AttendeeFactory extends Factory
{
	public function definition(): array
	{
		return [
			'event_id' => Event::factory(),
			'firstname' => fake()->firstName(),
			'lastname' => fake()->lastName(),
			'email' => fake()->safeEmail(),
			'attending' => true,
			'token' => Str::random(64),
		];
	}
}
