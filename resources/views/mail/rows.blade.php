{{-- One 28px row per person, like the lists on the page: name · (email) · answer --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
	@foreach ($attendees as $attendee)
		@php($cell = 'height: 20px; padding: 4px 16px 4px 0; vertical-align: middle; border-top: 1px solid '.($loop->first ? '#505028' : '#b4b49f').';'.($loop->last ? ' border-bottom: 1px solid #505028;' : ''))
		<tr>
			<td style="{{ $cell }}">{{ $attendee->name }}</td>
			@if ($withEmail)
				<td style="{{ $cell }}"><a href="mailto:{{ $attendee->email }}" style="color: #505028; text-decoration: none;">{{ $attendee->email }}</a></td>
			@endif
			<td align="right" style="{{ $cell }} padding-right: 8px; white-space: nowrap;">{{ $attendee->attending ? 'Ja gerne' : 'Leider nein' }}</td>
		</tr>
	@endforeach
</table>
