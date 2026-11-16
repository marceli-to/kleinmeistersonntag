<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlushAttendeesTest extends TestCase
{
	use RefreshDatabase;

	public function test_flush_deletes_entries_but_keeps_event_and_admins(): void
	{
		User::factory()->create();
		Attendee::factory()->count(3)->create();

		$this->artisan('attendees:flush')
			->expectsConfirmation('Delete all 3 entries? This cannot be undone.', 'yes')
			->expectsOutput('Deleted 3 entries.')
			->assertSuccessful();

		$this->assertDatabaseCount('attendees', 0);
		$this->assertSame(3, Event::count());
		$this->assertSame(1, User::count());
	}

	public function test_flush_can_be_cancelled(): void
	{
		Attendee::factory()->count(2)->create();

		$this->artisan('attendees:flush')
			->expectsConfirmation('Delete all 2 entries? This cannot be undone.', 'no')
			->assertSuccessful();

		$this->assertDatabaseCount('attendees', 2);
	}

	public function test_flush_with_force_skips_confirmation(): void
	{
		Attendee::factory()->count(2)->create();

		$this->artisan('attendees:flush --force')->assertSuccessful();

		$this->assertDatabaseCount('attendees', 0);
	}
}
