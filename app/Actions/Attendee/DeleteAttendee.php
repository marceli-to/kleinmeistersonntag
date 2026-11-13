<?php

namespace App\Actions\Attendee;

use App\Models\Attendee;

class DeleteAttendee
{
	public function execute(Attendee $attendee): void
	{
		$attendee->delete();
	}
}
