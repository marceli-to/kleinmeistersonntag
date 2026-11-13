<?php

namespace App\Http\Controllers;

use App\Actions\Attendee\DeleteAttendee;
use App\Actions\Attendee\StoreAttendees;
use App\Actions\Attendee\UpdateAttendee;
use App\Http\Requests\Attendee\StoreAttendeesRequest;
use App\Http\Requests\Attendee\UpdateAttendeeRequest;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class AttendeeController extends Controller
{
	public function store(StoreAttendeesRequest $request, StoreAttendees $action): RedirectResponse
	{
		$action->execute(
			Event::current(),
			$request->validated('persons'),
			$request->boolean('attending'),
		);

		return back();
	}

	public function update(UpdateAttendeeRequest $request, Attendee $attendee, UpdateAttendee $action): RedirectResponse
	{
		$action->execute($attendee, $request->validated());

		return back();
	}

	public function destroy(Attendee $attendee, DeleteAttendee $action): RedirectResponse
	{
		Gate::authorize('delete', $attendee);

		$action->execute($attendee);

		return back();
	}
}
