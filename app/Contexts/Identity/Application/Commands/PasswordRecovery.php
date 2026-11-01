<?php

namespace App\Contexts\Identity\Application\Commands;

use App\Contexts\Shared\Application\{WorkflowInput, WorkflowResult};
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\{DB, Hash, Password};
use Illuminate\Support\Str;

final class PasswordRecovery
{
    public function request(WorkflowInput $input): WorkflowResult
    {
        $input->validate(['email' => 'required|email|max:150']);
        try { Password::sendResetLink($input->only('email')); }
        catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $error) {
            \Illuminate\Support\Facades\Log::warning('Password recovery transport unavailable', ['exception' => get_class($error)]);
            return WorkflowResult::payload()->withErrors(['email' => ['Password reset email is temporarily unavailable. Please contact OPES support.']]);
        }
        return WorkflowResult::payload(['message' => 'If an eligible account exists, password reset instructions will be sent.']);
    }

    public function reset(WorkflowInput $input): WorkflowResult
    {
        $input->validate(['token' => 'required|string', 'email' => 'required|email|max:150',
            'password' => 'required|string|min:8|confirmed', 'password_confirmation' => 'required|string']);
        $status = Password::reset($input->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60)); $user->save();
            $ids = DB::table('oauth_access_tokens')->where('user_id', $user->id)->pluck('id');
            DB::table('oauth_refresh_tokens')->whereIn('access_token_id', $ids)->update(['revoked' => true]);
            DB::table('oauth_access_tokens')->where('user_id', $user->id)->update(['revoked' => true]);
            event(new PasswordReset($user));
        });
        if ($status !== Password::PASSWORD_RESET) return WorkflowResult::payload()->withErrors(['email' => [__($status)]]);
        return WorkflowResult::payload(['message' => __($status)]);
    }
}
