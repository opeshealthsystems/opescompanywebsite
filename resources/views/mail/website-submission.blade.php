<!doctype html><html lang="en"><body style="font-family:Arial,sans-serif;color:#102A43;line-height:1.6;max-width:760px;margin:auto;padding:24px">
<h1 style="font-size:24px">{{ $recovered ? 'Recovered website submission' : 'New website submission' }}</h1>
<p>Reference: <strong>{{ class_basename($submission) }} #{{ $submission->getKey() }}</strong></p>
@if($recovered)<p>This stored submission is being forwarded after the website notification routing was repaired. The original submission date is shown below.</p>@endif
<table cellpadding="10" style="border-collapse:collapse;width:100%">@foreach($fields as $label => $value)<tr><th align="left" valign="top" style="border-bottom:1px solid #CBD5E1;text-transform:capitalize">{{ $label }}</th><td style="border-bottom:1px solid #CBD5E1;white-space:pre-wrap">{{ $value }}</td></tr>@endforeach</table>
<p>Reply to this email to contact the submitter. You can also review the stored submission in the OPES administration panel.</p>
</body></html>
