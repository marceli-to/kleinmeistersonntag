<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
	use RefreshDatabase;

	public function test_guest_cannot_update_event(): void
	{
		$event = Event::factory()->create();

		$this->put("/events/{$event->id}", ['title' => 'Hacked'])->assertRedirect('/login');

		$this->assertSame('Weihnachtsessen 2026', $event->fresh()->title);
	}

	public function test_admin_can_update_event(): void
	{
		$event = Event::factory()->create();

		$this->actingAs(User::factory()->create())
			->put("/events/{$event->id}", [
				'title' => 'Weihnachtsessen 2027',
				'description' => "Zeile 1\nZeile 2",
			])
			->assertRedirect();

		$this->assertSame('Weihnachtsessen 2027', $event->fresh()->title);
		$this->assertSame("Zeile 1\nZeile 2", $event->fresh()->description);
	}
}
