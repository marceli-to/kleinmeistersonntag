<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('attendees:digest')
	->dailyAt(config('kleinmeistersonntag.digest_time'))
	->timezone(config('kleinmeistersonntag.timezone'));
