<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function show(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $input = new \App\Contexts\Shared\Application\WorkflowInput($request->all(), address: $request->ip());
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute('identity.password_reset', $input);
        if ($result->errors) return back()->withErrors($result->errors)->onlyInput('email');
        return redirect()->route('login')->with('status', $result->data['message']);
    }
}
