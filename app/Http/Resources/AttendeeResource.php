<?php

namespace App\Http\Resources;

use App\Support\GuestTokens;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Attendee */
class AttendeeResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		$own = app(GuestTokens::class)->owns($this->token);
		$editable = $own || $request->user() !== null;

		return [
			'id' => $this->id,
			'name' => $this->name,
			'attending' => $this->attending,
			'own' => $own,
			'editable' => $editable,
			// Personal data only for the owner or the admin
			'firstname' => $this->when($editable, $this->firstname),
			'lastname' => $this->when($editable, $this->lastname),
			'email' => $this->when($editable, $this->email),
		];
	}
}
