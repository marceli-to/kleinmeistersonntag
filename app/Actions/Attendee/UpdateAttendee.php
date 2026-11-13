<?php

namespace App\Actions\Attendee;

use App\Models\Attendee;

class UpdateAttendee
{
	/**
	 * @param array{firstname: string, lastname: string, email: string, attending: bool} $data
	 */
	public function execute(Attendee $attendee, array $data): Attendee
	{
		$attendee->update([
			'firstname' => trim($data['firstname']),
			'lastname' => trim($data['lastname']),
			'email' => trim($data['email']),
			'attending' => (bool) $data['attending'],
		]);

		return $attendee;
	}
}
