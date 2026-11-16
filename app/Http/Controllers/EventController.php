<?php

namespace App\Http\Controllers;

use App\Actions\Event\UpdateEvent;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\AttendeeResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
	public function show(): Response
	{
		$event = Event::current()->load('attendees');

		return Inertia::render('Event/Show', [
			'event' => new EventResource($event),
			// Alphabetical by last name (A–Z), case- and accent-insensitive
			'attendees' => AttendeeResource::collection(
				$event->attendees->sortBy(
					fn ($attendee) => Str::lower(Str::ascii($attendee->lastname.' '.$attendee->firstname))
				)->values()
			),
		]);
	}

	public function update(UpdateEventRequest $request, Event $event, UpdateEvent $action): RedirectResponse
	{
		$action->execute($event, $request->validated());

		return back();
	}
}
