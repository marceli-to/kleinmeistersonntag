<?php

return [

	/*
	| Receives the daily list of new entries (scheduled at 18:00 Europe/Zurich).
	| MAIL_TO in .env, several addresses separated by commas.
	*/
	'digest_recipients' => array_values(array_filter(array_map('trim', explode(',', (string) env('MAIL_TO', 'bf@wbg.ch'))))),

	'digest_time' => env('DIGEST_TIME', '18:00'),

	'timezone' => 'Europe/Zurich',

];
