<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Support\GuestTokens;
use Illuminate\Http\RedirectResponse;

/**
 * Signed link from the confirmation email: grants edit access on this device
 * (adds the entry's token to the cookie) and opens the edit modal.
 */
class EditLinkController extends Controller
{
	public function __invoke(Attendee $attendee, GuestTokens $tokens): RedirectResponse
	{
		$tokens->remember($attendee->token);

		return redirect()->route('event.show')->with('editAttendee', $attendee->id);
	}
}
