<?php

namespace Tests\Feature;

use App\Mail\AttendeeConfirmation;
use App\Mail\DailyDigest;
use App\Models\Attendee;
use App\Models\Event;
use App\Support\GuestTokens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MailTest extends TestCase
{
	use RefreshDatabase;

	private Event $event;

	protected function setUp(): void
	{
		parent::setUp();

		$this->withoutVite();
		$this->event = Event::factory()->create();
	}

	public function test_registration_sends_one_confirmation_per_email_address(): void
	{
		Mail::fake();

		$this->post('/attendees', [
			'persons' => [
				['firstname' => 'Erstes', 'lastname' => 'Beispiel', 'email' => 'eb@beispiel.net'],
				['firstname' => 'Zweites', 'lastname' => 'Beispiel', 'email' => 'EB@beispiel.net'],
				['firstname' => 'Drittes', 'lastname' => 'Beispiel', 'email' => 'db@beispiel.net'],
			],
			'attending' => true,
		])->assertRedirect();

		Mail::assertSentCount(2);
		Mail::assertSent(AttendeeConfirmation::class, fn ($mail) => $mail->hasTo('eb@beispiel.net') && $mail->attendees->count() === 2);
		Mail::assertSent(AttendeeConfirmation::class, fn ($mail) => $mail->hasTo('db@beispiel.net') && $mail->attendees->count() === 1);
	}

	public function test_confirmation_contains_signed_edit_link(): void
	{
		$attendee = Attendee::factory()->for($this->event)->create();

		$html = (new AttendeeConfirmation($this->event, collect([$attendee])))->render();

		$this->assertStringContainsString('Eintrag bearbeiten', $html);
		$this->assertStringContainsString("/eintrag/{$attendee->id}/bearbeiten?signature=", $html);
	}

	public function test_edit_link_grants_access_and_opens_modal(): void
	{
		$attendee = Attendee::factory()->for($this->event)->create();
		$url = URL::signedRoute('attendees.edit', $attendee);

		$this->get($url)->assertRedirect('/')->assertCookie(GuestTokens::COOKIE);

		$this->withCookie(GuestTokens::COOKIE, json_encode([$attendee->token]))
			->withSession(['editAttendee' => $attendee->id])
			->get('/')
			->assertInertia(fn (Assert $page) => $page
				->where('editAttendee', $attendee->id)
				->where('attendees.0.editable', true)
			);
	}

	public function test_edit_link_requires_valid_signature(): void
	{
		$attendee = Attendee::factory()->for($this->event)->create();

		$this->get("/eintrag/{$attendee->id}/bearbeiten")->assertForbidden();
		$this->get("/eintrag/{$attendee->id}/bearbeiten?signature=nope")->assertForbidden();
	}

	public function test_edit_link_for_deleted_entry_redirects_home(): void
	{
		$attendee = Attendee::factory()->for($this->event)->create();
		$url = URL::signedRoute('attendees.edit', $attendee);
		$attendee->delete();

		$this->get($url)->assertRedirect('/');
	}

	public function test_digest_lists_only_new_entries(): void
	{
		Mail::fake();

		Attendee::factory()->for($this->event)->create(['created_at' => now()->subDays(2)]);
		$new = Attendee::factory()->for($this->event)->create(['created_at' => now()->subHour()]);

		$this->artisan('attendees:digest')->assertSuccessful();

		Mail::assertSent(DailyDigest::class, fn ($mail) => $mail->hasTo('bf@wbg.ch')
			&& $mail->attendees->pluck('id')->all() === [$new->id]);
	}

	public function test_digest_goes_to_all_mail_to_addresses(): void
	{
		Mail::fake();
		config(['kleinmeistersonntag.digest_recipients' => ['a@wbg.ch', 'b@wbg.ch']]);

		Attendee::factory()->for($this->event)->create(['created_at' => now()->subHour()]);

		$this->artisan('attendees:digest')->assertSuccessful();

		Mail::assertSent(DailyDigest::class, fn ($mail) => $mail->hasTo('a@wbg.ch') && $mail->hasTo('b@wbg.ch'));
	}

	public function test_digest_is_skipped_without_new_entries(): void
	{
		Mail::fake();

		Attendee::factory()->for($this->event)->create(['created_at' => now()->subDays(2)]);

		$this->artisan('attendees:digest')->assertSuccessful();

		Mail::assertNothingSent();
	}
}
