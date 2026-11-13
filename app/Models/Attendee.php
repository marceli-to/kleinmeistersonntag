<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendee extends Model
{
	use HasFactory;

	protected $fillable = [
		'firstname',
		'lastname',
		'email',
		'attending',
		'token',
	];

	protected $hidden = [
		'token',
	];

	protected function casts(): array
	{
		return [
			'attending' => 'boolean',
		];
	}

	public function event(): BelongsTo
	{
		return $this->belongsTo(Event::class);
	}

	public function getNameAttribute(): string
	{
		return trim("{$this->firstname} {$this->lastname}");
	}
}
