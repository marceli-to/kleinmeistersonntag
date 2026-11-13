<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
	/** @var array<int, array{name: string, email: string}> */
	private const ADMINS = [
		['name' => 'Marcel Stadelmann', 'email' => 'm@marceli.to'],
		['name' => 'Benedikt Flüeler', 'email' => 'bf@wbg.ch'],
	];

	/**
	 * Creates missing admins with a random password (printed once).
	 * Existing admins are left untouched, so re-running never resets a password.
	 */
	public function run(): void
	{
		foreach (self::ADMINS as $admin) {
			if (User::where('email', $admin['email'])->exists()) {
				$this->command?->line("  {$admin['email']}: exists, unchanged");

				continue;
			}

			$password = self::password();

			User::create([
				'name' => $admin['name'],
				'email' => $admin['email'],
				'password' => $password,
			]);

			$this->command?->info("  {$admin['email']}: {$password}");
		}
	}

	/** Format XXXX-XXXX-XXXX-XXXX, uppercase letters only. */
	public static function password(): string
	{
		$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$blocks = array_map(
			fn () => implode('', array_map(fn () => $letters[random_int(0, 25)], range(1, 4))),
			range(1, 4),
		);

		return implode('-', $blocks);
	}
}
