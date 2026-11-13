<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
	use RefreshDatabase;

	public function test_password_format(): void
	{
		foreach (range(1, 50) as $i) {
			$this->assertMatchesRegularExpression('/^[A-Z]{4}-[A-Z]{4}-[A-Z]{4}-[A-Z]{4}$/', AdminSeeder::password());
		}
	}

	public function test_creates_both_admins_and_never_resets_passwords(): void
	{
		$this->seed(AdminSeeder::class);

		$this->assertDatabaseHas('users', ['email' => 'm@marceli.to', 'name' => 'Marcel Stadelmann']);
		$this->assertDatabaseHas('users', ['email' => 'bf@wbg.ch', 'name' => 'Benedikt Flüeler']);

		$hash = User::where('email', 'm@marceli.to')->value('password');
		$this->seed(AdminSeeder::class);

		$this->assertSame($hash, User::where('email', 'm@marceli.to')->value('password'));
		$this->assertDatabaseCount('users', 2);
	}
}
