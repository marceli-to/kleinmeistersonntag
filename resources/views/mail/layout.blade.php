{{--
	Email layout: white, left-aligned, no header. Mirrors the page design
	(olive text, 11px bold labels, hairline rows, outlined button) with Inter
	instead of the brand fonts (web fonts are unreliable in mail clients).
--}}
<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="color-scheme" content="light only">
	<meta name="supported-color-schemes" content="light only">
	<title>{{ $title ?? config('app.name') }}</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
	<style>
		body { margin: 0; padding: 0; background: #ffffff; }
		a { color: #505028; }
		.button a:hover { background: #e65000 !important; border-color: #e65000 !important; color: #ffffff !important; }
		@media (max-width: 600px) { .frame { padding: 32px 24px !important; } }
	</style>
</head>
@php($font = "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif")
<body style="margin: 0; padding: 0; background: #ffffff; color: #505028; font-family: {{ $font }}; font-size: 15px; line-height: 20px; -webkit-font-smoothing: antialiased;">
	<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background: #ffffff;">
		<tr>
			<td class="frame" align="left" style="padding: 48px 32px; text-align: left;">
				<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 512px; color: #505028; font-family: {{ $font }}; font-size: 15px; line-height: 20px;">
					@yield('content')
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
