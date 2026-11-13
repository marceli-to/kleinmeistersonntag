<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
	use RefreshDatabase;

	public function test_admin_can_log_in_and_out(): void
	{
		$this->withoutVite();
		$user = User::factory()->create(['email' => 'admin@example.com']);

		$this->get('/login')->assertOk();

		$this->post('/login', ['email' => 'admin@example.com', 'password' => 'password'])
			->assertRedirect('/');

		$this->assertAuthenticatedAs($user);

		$this->post('/logout')->assertRedirect('/');

		$this->assertGuest();
	}

	public function test_wrong_password_is_rejected(): void
	{
		User::factory()->create(['email' => 'admin@example.com']);

		$this->post('/login', ['email' => 'admin@example.com', 'password' => 'wrong'])
			->assertSessionHasErrors('email');

		$this->assertGuest();
	}
}
