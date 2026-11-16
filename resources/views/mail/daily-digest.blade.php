@extends('mail.layout', ['title' => $event->title])

@section('content')
	@include('mail.title', ['event' => $event])

	<tr>
		<td style="padding-top: 40px;">
			@include('mail.label', ['text' => 'Neue Einträge seit '.$since->copy()->timezone($tz)->format('d.m.Y, H:i')])
			@include('mail.rows', ['attendees' => $attendees, 'withEmail' => true])
		</td>
	</tr>

	<tr>
		<td style="padding-top: 28px;">
			@include('mail.label', ['text' => 'Stand'])
			<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td style="height: 28px; border-top: 1px solid #505028;">Zugesagt</td>
					<td align="right" style="height: 28px; border-top: 1px solid #505028; padding-right: 8px;">{{ $accepted }}</td>
				</tr>
				<tr>
					<td style="height: 28px; border-top: 1px solid #b4b49f; border-bottom: 1px solid #505028;">Abgesagt</td>
					<td align="right" style="height: 28px; border-top: 1px solid #b4b49f; border-bottom: 1px solid #505028; padding-right: 8px;">{{ $declined }}</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td style="padding-top: 32px;">
			@include('mail.button', ['url' => route('event.show'), 'text' => 'Zur Anmeldeseite'])
		</td>
	</tr>
@endsection
