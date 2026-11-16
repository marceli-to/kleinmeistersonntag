<?php

namespace App\Actions\Attendee;

use App\Mail\AttendeeConfirmation;
use App\Models\Attendee;
use App\Models\Event;
use App\Support\GuestTokens;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class StoreAttendees
{
	public function __construct(private GuestTokens $tokens)
	{
	}

	/**
	 * @param array<int, array{firstname: string, lastname: string, email: string}> $persons
	 */
	public function execute(Event $event, array $persons, bool $attending): Collection
	{
		$token = $this->tokens->generate();

		$attendees = DB::transaction(fn () => collect($persons)->map(
			fn (array $person) => $event->attendees()->create([
				'firstname' => trim($person['firstname']),
				'lastname' => trim($person['lastname']),
				'email' => trim($person['email']),
				'attending' => $attending,
				'token' => $token,
			])
		));

		$this->tokens->remember($token);

		$this->sendConfirmations($event, $attendees);

		return $attendees;
	}

	/**
	 * One email per address (persons sharing an address get a single email).
	 * A mail failure must not break the signup, so it's only reported.
	 *
	 * @param Collection<int, Attendee> $attendees
	 */
	private function sendConfirmations(Event $event, Collection $attendees): void
	{
		$attendees
			->groupBy(fn (Attendee $attendee) => mb_strtolower($attendee->email))
			->each(function (Collection $group) use ($event) {
				try {
					Mail::to($group->first()->email)->send(new AttendeeConfirmation($event, $group->values()));
				} catch (Throwable $e) {
					report($e);
				}
			});
	}
}
