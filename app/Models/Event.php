<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
	use HasFactory;

	protected $fillable = [
		'title',
		'description',
	];

	public function attendees(): HasMany
	{
		return $this->hasMany(Attendee::class)->oldest()->oldest('id');
	}

	public static function current(): self
	{
		return static::latest('id')->firstOrFail();
	}
}
