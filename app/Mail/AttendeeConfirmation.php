<?php

namespace App\Mail;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

/**
 * Sent to each email address of a submission. Lists the persons entered with
 * that address and links to the page with the edit modal open.
 */
class AttendeeConfirmation extends Mailable
{
	use Queueable, SerializesModels;

	/** @param Collection<int, Attendee> $attendees */
	public function __construct(public Event $event, public Collection $attendees)
	{
	}

	public function envelope(): Envelope
	{
		return new Envelope(
			subject: "{$this->event->title} – {$this->answerLabel()}",
		);
	}

	public function content(): Content
	{
		return new Content(
			view: 'mail.attendee-confirmation',
			with: [
				'answerLabel' => $this->answerLabel(),
				'editUrl' => URL::signedRoute('attendees.edit', $this->attendees->first()),
			],
		);
	}

	private function answerLabel(): string
	{
		return $this->attendees->count() > 1 ? 'Eure Antwort' : 'Deine Antwort';
	}
}
