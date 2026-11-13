<?php

namespace App\Actions\Event;

use App\Models\Event;

class UpdateEvent
{
	/**
	 * @param array{title: string, description: ?string} $data
	 */
	public function execute(Event $event, array $data): Event
	{
		$event->update([
			'title' => trim($data['title']),
			'description' => isset($data['description']) ? trim($data['description']) : null,
		]);

		return $event;
	}
}
