<?php

namespace App\Http\Requests\Attendee;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendeesRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		return [
			'persons' => ['required', 'array', 'min:1', 'max:10'],
			'persons.*.firstname' => ['required', 'string', 'max:100'],
			'persons.*.lastname' => ['required', 'string', 'max:100'],
			'persons.*.email' => ['required', 'string', 'email', 'max:255'],
			'attending' => ['required', 'boolean'],
		];
	}
}
