<tr>
	<td style="font-size: 23px; line-height: 28px; font-weight: 500; letter-spacing: -0.01em;">{{ $event->title }}</td>
</tr>
@if ($event->description)
	<tr>
		<td style="padding-top: 8px; white-space: pre-line;">{{ $event->description }}</td>
	</tr>
@endif
