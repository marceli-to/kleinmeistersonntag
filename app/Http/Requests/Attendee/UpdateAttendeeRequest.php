<?php

namespace App\Http\Requests\Attendee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAttendeeRequest extends FormRequest
{
	public function authorize(): bool
	{
		return Gate::allows('update', $this->route('attendee'));
	}

	public function rules(): array
	{
		return [
			'firstname' => ['required', 'string', 'max:100'],
			'lastname' => ['required', 'string', 'max:100'],
			'email' => ['required', 'string', 'email', 'max:255'],
			'attending' => ['required', 'boolean'],
		];
	}
}
