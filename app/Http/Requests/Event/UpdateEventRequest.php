<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateEventRequest extends FormRequest
{
	public function authorize(): bool
	{
		return Gate::allows('update', $this->route('event'));
	}

	public function rules(): array
	{
		return [
			'title' => ['required', 'string', 'max:255'],
			'description' => ['nullable', 'string', 'max:2000'],
		];
	}
}
