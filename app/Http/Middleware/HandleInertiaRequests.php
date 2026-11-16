<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
	protected $rootView = 'app';

	public function version(Request $request): ?string
	{
		return parent::version($request);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function share(Request $request): array
	{
		return [
			...parent::share($request),
			'isAdmin' => $request->user() !== null,
			// Set by the edit link in the confirmation email → opens the edit modal
			'editAttendee' => fn () => $request->session()->get('editAttendee'),
		];
	}
}
