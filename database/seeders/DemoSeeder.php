<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
	public function run(): void
	{
		$event = Event::current();

		if ($event->attendees()->exists()) {
			return;
		}

		$people = [
			['Kasimir', 'Platzhalter', true],
			['Fiona', 'Platzhalter-Beispielsen', true],
			['Annabarbara', 'di Bellaroba', true],
			['Giancarlo', 'die Bellaroba', true],
			['Stefan', 'Wunder', false],
			['Wilhelm', 'Schmid', false],
			['Massimliano', 'Marchapiedi', false],
			['Holger', 'Humbeldeyck', false],
		];

		foreach ($people as [$firstname, $lastname, $attending]) {
			$event->attendees()->create([
				'firstname' => $firstname,
				'lastname' => $lastname,
				'email' => Str::slug("{$firstname}.{$lastname}", '.').'@example.com',
				'attending' => $attending,
				'token' => Str::random(64),
			]);
		}
	}
}
