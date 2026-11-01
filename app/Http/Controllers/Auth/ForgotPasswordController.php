<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot-password');
    }

    public function submit(Request $request)
    {
        $input = new \App\Contexts\Shared\Application\WorkflowInput($request->all(), address: $request->ip());
        $result = app(\App\Contexts\Shared\Infrastructure\WorkflowDispatcher::class)->execute('identity.password_request', $input);
        if ($result->errors) return back()->withErrors($result->errors)->onlyInput('email');
        return back()->with('status', $result->data['message']);
    }
}
