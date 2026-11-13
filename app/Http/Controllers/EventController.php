<?php

namespace App\Http\Controllers;

use App\Actions\Event\UpdateEvent;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\AttendeeResource;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
	public function show(): Response
	{
		$event = Event::current()->load('attendees');

		return Inertia::render('Event/Show', [
			'event' => new EventResource($event),
			'attendees' => AttendeeResource::collection($event->attendees),
		]);
	}

	public function update(UpdateEventRequest $request, Event $event, UpdateEvent $action): RedirectResponse
	{
		$action->execute($event, $request->validated());

		return back();
	}
}
