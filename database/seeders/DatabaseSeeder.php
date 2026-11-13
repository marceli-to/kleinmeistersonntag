<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	public function run(): void
	{
		if (Event::doesntExist()) {
			Event::create([
				'title' => 'Weihnachtsessen 2026',
				'description' => "Da Michelangelo, Gertrudstrasse 37, 8003 Zürich\nSonntag, 13.\u{202F}Dezember 2026, 12.30 Uhr",
			]);
		}

		$this->call(AdminSeeder::class);

		if (app()->isLocal()) {
			$this->call(DemoSeeder::class);
		}
	}
}
