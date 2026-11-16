<?php

namespace App\Console\Commands;

use App\Mail\DailyDigest;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyDigest extends Command
{
	protected $signature = 'attendees:digest {--hours=24 : Look back this many hours}';

	protected $description = 'Email the owner a list of entries created in the last 24 hours';

	public function handle(): int
	{
		$since = now()->subHours((int) $this->option('hours'));
		$recipients = config('kleinmeistersonntag.digest_recipients');
		$sent = 0;

		if (empty($recipients)) {
			$this->error('MAIL_TO is empty, nothing sent.');

			return self::FAILURE;
		}

		Event::with(['attendees' => fn ($query) => $query->where('created_at', '>=', $since)])
			->get()
			->filter(fn (Event $event) => $event->attendees->isNotEmpty())
			->each(function (Event $event) use ($recipients, $since, &$sent) {
				Mail::to($recipients)->send(new DailyDigest($event, $event->attendees, $since));
				$sent++;
				$this->info("{$event->title}: {$event->attendees->count()} new → ".implode(', ', $recipients));
			});

		if ($sent === 0) {
			$this->info('No new entries, nothing sent.');
		}

		return self::SUCCESS;
	}
}
