<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
	@foreach ($attendees as $attendee)
		@php($border = $loop->first ? '#505028' : '#b4b49f')
		<tr>
			<td style="padding: 4px 16px 4px 0; border-top: 1px solid {{ $border }};{{ $loop->last ? ' border-bottom: 1px solid #505028;' : '' }}">
				{{ $attendee->name }}
				@if ($withEmail)
					<br><a href="mailto:{{ $attendee->email }}" style="color: #505028; text-decoration: none; font-size: 11px; line-height: 16px;">{{ $attendee->email }}</a>
				@endif
			</td>
			<td align="right" valign="top" style="padding: 4px 8px 4px 0; border-top: 1px solid {{ $border }};{{ $loop->last ? ' border-bottom: 1px solid #505028;' : '' }} white-space: nowrap;">
				{{ $attendee->attending ? 'Ja gerne' : 'Leider nein' }}
			</td>
		</tr>
	@endforeach
</table>
