@extends('mail.layout', ['title' => $event->title])

@section('content')
	@include('mail.title', ['event' => $event])

	<tr>
		<td style="padding-top: 40px; font-weight: 500;">
			Danke{{ $attendees->count() > 1 ? ' Euch' : '' }}, {{ $attendees->count() > 1 ? 'Eure' : 'Deine' }} Antwort ist eingetragen.
		</td>
	</tr>

	<tr>
		<td style="padding-top: 20px;">
			@include('mail.label', ['text' => $answerLabel])
			@include('mail.rows', ['attendees' => $attendees, 'withEmail' => false])
		</td>
	</tr>

	<tr>
		<td style="padding-top: 32px;">
			@include('mail.button', ['url' => $editUrl, 'text' => 'Eintrag bearbeiten'])
		</td>
	</tr>

	<tr>
		<td style="padding-top: 12px; font-size: 11px; line-height: 16px; letter-spacing: 0.02em;">
			Über diesen Link {{ $attendees->count() > 1 ? 'könnt Ihr Eure' : 'kannst Du Deine' }} Angaben jederzeit ändern.
		</td>
	</tr>
@endsection
