<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Authorize application | OPES Health Systems</title>
<style>body{font:1rem/1.6 system-ui,sans-serif;background:#f4f7fa;color:#102a43;margin:0;padding:2rem}main{max-width:36rem;margin:3rem auto;background:white;border:1px solid #cbd5df;border-radius:.5rem;padding:2rem}button{font:inherit;padding:.65rem 1.2rem;border:1px solid #143c64;border-radius:.25rem;background:#143c64;color:white;cursor:pointer}form{display:inline-block;margin-right:1rem}button.deny{background:white;color:#143c64}button:focus-visible{outline:3px solid #1473b5;outline-offset:3px}</style></head>
<body><main><h1>Authorize {{ $client->name }}</h1>
<p>You are signed in as {{ $user->email }}. This application is requesting these permissions:</p>
<ul>@foreach($scopes as $scope)<li>{{ $scope->description ?: $scope->id }}</li>@endforeach</ul>
<p>Your account permissions continue to limit what the application can access.</p>
<form method="post" action="{{ route('passport.authorizations.approve') }}">@csrf<input type="hidden" name="state" value="{{ $request->state }}"><input type="hidden" name="client_id" value="{{ $client->id }}"><input type="hidden" name="auth_token" value="{{ $authToken }}"><button type="submit">Allow access</button></form>
<form method="post" action="{{ route('passport.authorizations.deny') }}">@csrf @method('DELETE')<input type="hidden" name="state" value="{{ $request->state }}"><input type="hidden" name="client_id" value="{{ $client->id }}"><input type="hidden" name="auth_token" value="{{ $authToken }}"><button class="deny" type="submit">Deny</button></form>
</main></body></html>
