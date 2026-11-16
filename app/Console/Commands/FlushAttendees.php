<?php

namespace App\Console\Commands;

use App\Models\Attendee;
use Illuminate\Console\Command;

/**
 * For testing before go-live: removes all entries (guests), keeps the event
 * and the admin accounts. Remove this command once the site is in use.
 */
class FlushAttendees extends Command
{
	protected $signature = 'attendees:flush {--force : Skip the confirmation}';

	protected $description = 'Delete all entries (keeps the event and the admins) – for testing only';

	public function handle(): int
	{
		$count = Attendee::count();

		if ($count === 0) {
			$this->info('No entries, nothing to delete.');

			return self::SUCCESS;
		}

		if (! $this->option('force') && ! $this->confirm("Delete all {$count} entries? This cannot be undone.")) {
			$this->info('Cancelled.');

			return self::SUCCESS;
		}

		Attendee::query()->delete();

		$this->info("Deleted {$count} entries.");

		return self::SUCCESS;
	}
}
