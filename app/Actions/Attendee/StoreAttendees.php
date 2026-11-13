<?php

namespace App\Actions\Attendee;

use App\Models\Event;
use App\Support\GuestTokens;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

		return $attendees;
	}
}
