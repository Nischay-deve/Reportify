<?php

namespace App\Services;

namespace App\Services;

use App\Models\TdtyUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
// use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    /**
     * Send verification email
     *
     * @param TdtyUser $user
     * @param string $slug
     */
    public static function sendUserVerificationEmail($user, $slug)
    {
        $verificationLink = route('verify.email', ['slug' => $slug, 'code' => $user->verification_code]);
        $siteName = config("app.name");

        $emailBody = "<h2>Account Verification</h2>
        <p>Thank you for choosing {$siteName}! Please confirm your email address by clicking the link below.</p>
        <p>
        <a style='width:200px; background-color: rgb(67, 155, 115) !important; border-radius: 4px; text-align:center; text-decoration: none; color: rgb(255, 255, 255) !important; line-height: 42px; display: block;' target='_blank' href='{$verificationLink}' title='{$verificationLink}'>Verify your email address</a>
        </p>
        <p>If you did not sign up for a {$siteName} account, you can simply disregard this email.</p>
        <p><br/></p>
        <p>{$siteName} Team</p>";

        $subject = config('app.name') . ' - Please verify your account!';

        if (config('app.allow_mail_sending')) {
            Mail::send([], [], function ($message) use ($user, $subject, $emailBody) {
                $message->to($user->email)
                    ->subject($subject)
                    ->setBody($emailBody, 'text/html'); // Set email content type to HTML
            });
        }
    }


    public static function sendResetPasswordEmail($user, $slug, $token)
    {
        $resetLink = route('reset.password.form', ['slug' => $slug, 'token' => $token]);
        $siteName = config("app.name");

        $emailBody = "<h2>Reset Password</h2>
        <p>Please reset your password by clicking the link below.</p>
        <p>
        <a style='width:200px; background-color: rgb(67, 155, 115) !important; border-radius: 4px; text-align:center; text-decoration: none; color: rgb(255, 255, 255) !important; line-height: 42px; display: block;' target='_blank' href='{$resetLink}' title='{$resetLink}'>Reset Password</a>
        </p>
        <p>If you did not initiated this, you can simply disregard this email.</p>
        <p><br/></p>
        <p>{$siteName} Team</p>";

        $subject = config('app.name') . ' - Reset Password!';

        if (config('app.allow_mail_sending')) {
            try {
                Mail::send([], [], function ($message) use ($user, $subject, $emailBody) {
                    $message->to($user->email)
                        ->subject($subject)
                        ->setBody($emailBody, 'text/html'); // Set email content type to HTML
                });
            } catch (Exception $e) {
                Log::error('sendResetPasswordEmail sendEmail() Exception: ' . $e->getMessage());
            }
        }
    }
}
