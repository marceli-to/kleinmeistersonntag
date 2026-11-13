<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/**
 * Guests don't log in. Each submission gets a random token which is stored
 * in an (encrypted) cookie, so the guest can edit their own entries later.
 */
class GuestTokens
{
	public const COOKIE = 'kms_entries';

	public function __construct(private Request $request)
	{
	}

	/** @return array<int, string> */
	public function all(): array
	{
		$tokens = json_decode((string) $this->request->cookie(self::COOKIE), true);

		return is_array($tokens) ? array_values(array_filter($tokens, 'is_string')) : [];
	}

	public function owns(string $token): bool
	{
		return in_array($token, $this->all(), true);
	}

	public function generate(): string
	{
		return Str::random(64);
	}

	public function remember(string $token): void
	{
		$tokens = array_values(array_unique([...$this->all(), $token]));

		Cookie::queue(Cookie::forever(self::COOKIE, json_encode($tokens)));
	}
}
