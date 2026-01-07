<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class PasswordResetController extends Controller
{
    // Show forgot password form
    public function showForgotForm()
    {
        return view('auth.forgot-password')->with([
            'title' => 'Forgot Password - COMS'
        ]);
    }

    // Send reset email
public function sendResetLink(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $token = Str::random(64);

    // Delete old tokens
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    // Insert new token
    DB::table('password_reset_tokens')->insert([
        'email'      => $request->email,
        'token'      => Hash::make($token),
        'created_at' => Carbon::now(),
    ]);

    $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

    // Send email using html()
    Mail::html(
        "<p>Click the link below to reset your password:</p>
         <p><a href='{$resetLink}'>{$resetLink}</a></p>
         <p>If you did not request this, ignore this email.</p>",
        function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Reset Your COMS Password');
        }
    );

    return back()->with('success', 'Password reset link sent to your email.');
}


    // Show reset form
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $email,
            'title' => 'Reset Password - COMS'
        ]);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'token'    => 'required',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token']);
        }

        // Update password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully. You can now login.');
    }
}

// For Real Email Edit .env file
// MAIL_MAILER=smtp
//MAIL_HOST=smtp.gmail.com
//MAIL_PORT=587
//MAIL_USERNAME=your_email@gmail.com
//MAIL_PASSWORD=your_email_password_or_app_password
//MAIL_ENCRYPTION=tls
//MAIL_FROM_ADDRESS="your_email@gmail.com"
//MAIL_FROM_NAME="COMS"
