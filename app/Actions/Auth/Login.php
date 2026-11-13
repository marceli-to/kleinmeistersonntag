<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login
{
	private const MAX_ATTEMPTS = 5;

	public function execute(Request $request, string $email, string $password): void
	{
		$key = Str::lower($email).'|'.$request->ip();

		if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
			throw ValidationException::withMessages([
				'email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($key)]),
			]);
		}

		if (! Auth::attempt(['email' => $email, 'password' => $password], true)) {
			RateLimiter::hit($key);

			throw ValidationException::withMessages([
				'email' => __('auth.failed'),
			]);
		}

		RateLimiter::clear($key);
		$request->session()->regenerate();
	}
}
