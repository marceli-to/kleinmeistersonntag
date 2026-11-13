<?php

namespace Tests\Feature;

use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use App\Support\GuestTokens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AttendeeTest extends TestCase
{
	use RefreshDatabase;

	private Event $event;

	protected function setUp(): void
	{
		parent::setUp();

		$this->withoutVite();
		$this->event = Event::factory()->create();
	}

	public function test_page_renders_attendees_without_private_data(): void
	{
		Attendee::factory()->for($this->event)->create(['firstname' => 'Kasimir', 'lastname' => 'Platzhalter']);

		$this->get('/')
			->assertOk()
			->assertInertia(fn (Assert $page) => $page
				->component('Event/Show')
				->where('event.title', 'Weihnachtsessen 2026')
				->where('attendees.0.name', 'Kasimir Platzhalter')
				->where('attendees.0.editable', false)
				->missing('attendees.0.email')
				->missing('attendees.0.token')
			);
	}

	public function test_guest_can_register_multiple_persons_and_gets_a_cookie(): void
	{
		$response = $this->post('/attendees', [
			'persons' => [
				['firstname' => 'Erfasstes', 'lastname' => 'Beispiel', 'email' => 'eb@beispiel.net'],
				['firstname' => 'Zweites', 'lastname' => 'Beispiel', 'email' => 'zb@beispiel.net'],
			],
			'attending' => false,
		]);

		$response->assertRedirect()->assertCookie(GuestTokens::COOKIE);

		$this->assertDatabaseCount('attendees', 2);
		$this->assertSame(1, Attendee::distinct()->count('token'));
		$this->assertFalse(Attendee::first()->attending);
	}

	public function test_registration_is_validated(): void
	{
		$this->post('/attendees', [
			'persons' => [['firstname' => '', 'lastname' => 'Beispiel', 'email' => 'nope']],
			'attending' => true,
		])->assertSessionHasErrors(['persons.0.firstname', 'persons.0.email']);

		$this->assertDatabaseCount('attendees', 0);
	}

	public function test_guest_can_edit_own_entry(): void
	{
		$attendee = Attendee::factory()->for($this->event)->create();

		$this->withCookie(GuestTokens::COOKIE, json_encode([$attendee->token]))
			->get('/')
			->assertInertia(fn (Assert $page) => $page
				->where('attendees.0.own', true)
				->where('attendees.0.email', $attendee->email)
			);

		$this->withCookie(GuestTokens::COOKIE, json_encode([$attendee->token]))
			->put("/attendees/{$attendee->id}", [
				'firstname' => 'Neu',
				'lastname' => 'Name',
				'email' => 'neu@beispiel.net',
				'attending' => false,
			])
			->assertRedirect();

		$this->assertSame('Neu Name', $attendee->fresh()->name);
		$this->assertFalse($attendee->fresh()->attending);
	}

	public function test_guest_cannot_edit_or_delete_other_entries(): void
	{
		$attendee = Attendee::factory()->for($this->event)->create();

		$this->put("/attendees/{$attendee->id}", [
			'firstname' => 'Neu',
			'lastname' => 'Name',
			'email' => 'neu@beispiel.net',
			'attending' => false,
		])->assertForbidden();

		$this->withCookie(GuestTokens::COOKIE, json_encode([$attendee->token]))
			->delete("/attendees/{$attendee->id}")
			->assertRedirect('/login');

		$this->assertModelExists($attendee);
	}

	public function test_admin_can_edit_and_delete_any_entry(): void
	{
		$admin = User::factory()->create();
		$attendee = Attendee::factory()->for($this->event)->create();

		$this->actingAs($admin)
			->put("/attendees/{$attendee->id}", [
				'firstname' => 'Admin',
				'lastname' => 'Edit',
				'email' => 'ae@beispiel.net',
				'attending' => true,
			])
			->assertRedirect();

		$this->assertSame('Admin Edit', $attendee->fresh()->name);

		$this->actingAs($admin)->delete("/attendees/{$attendee->id}")->assertRedirect();

		$this->assertModelMissing($attendee);
	}
}
