<?php

namespace App\Policies;

use App\Models\Attendee;
use App\Models\User;
use App\Support\GuestTokens;

class AttendeePolicy
{
	public function __construct(private GuestTokens $tokens)
	{
	}

	public function update(?User $user, Attendee $attendee): bool
	{
		return $user !== null || $this->tokens->owns($attendee->token);
	}

	public function delete(?User $user, Attendee $attendee): bool
	{
		return $user !== null;
	}
}
