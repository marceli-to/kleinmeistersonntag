<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
	public function definition(): array
	{
		return [
			'title' => 'Weihnachtsessen 2026',
			'description' => 'Da Michelangelo, Gertrudstrasse 37, 8003 Zürich',
		];
	}
}
