<?php

namespace App\Mail;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Daily list of new entries for the event owner.
 */
class DailyDigest extends Mailable
{
	use Queueable, SerializesModels;

	/** @param Collection<int, Attendee> $attendees */
	public function __construct(public Event $event, public Collection $attendees, public Carbon $since)
	{
	}

	public function envelope(): Envelope
	{
		$count = $this->attendees->count();

		return new Envelope(
			subject: "{$this->event->title} – {$count} ".($count === 1 ? 'neuer Eintrag' : 'neue Einträge'),
		);
	}

	public function content(): Content
	{
		return new Content(
			view: 'mail.daily-digest',
			with: [
				'accepted' => $this->event->attendees()->where('attending', true)->count(),
				'declined' => $this->event->attendees()->where('attending', false)->count(),
				'tz' => config('kleinmeistersonntag.timezone'),
			],
		);
	}
}
