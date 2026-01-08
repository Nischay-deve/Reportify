<?php

namespace App\Services;

use App\Models\TdtyUser;
use App\Models\User;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;

class UserEmailService
{


    public static function sendEmail($toUserEmail, $toUserName, $data)
    {

        // dd($data);

        $email = new Mail();
        $email->setFrom(config('app.sendgrid_from_email'), config('app.sendgrid_from_name'));
        $email->addTo(
            $toUserEmail,
            $toUserName,
            $data,
            0
        );
        $email->setTemplateId($data['template_id']);

        $sendgrid = new \SendGrid(config('app.sendgrid_api_key'));
        try {
            $response = $sendgrid->send($email);
            // print $response->statusCode() . "\n";
            // print_r($response->headers());
            // print $response->body() . "\n";
        } catch (Exception $e) {
            // echo 'Caught exception: '.  $e->getMessage(). "\n";
            Log::error('Twilio sendEmail() Exception: ' . $e->getMessage());
        }
    }


    /**
     * Send verification email
     *
     * @param User $user
     */
    public static function sendVerificationEmail(User $user, $token)
    {
        $link = url('/verify-email', $token) . '?email=' . urlencode($user->getEmailForPasswordReset());
        $data = array();
        $data["app_name"] = config('app.name');
        $data["subject"] = config('app.name') . ' - Confirm your user registration';
        $data["template_id"] = config('app.sendgrid_signup_template');
        $data["name"] = $user->name;
        $data["email"] = $user->email;
        $data["link"] = $link;
        self::sendEmail($user->email, $user->name, $data);
    }


    /* Send password reset email
    *
    * @param User $user
    * @param string $token
    */
    public static function sendPasswordReset(User $user, $token)
    {
        $link = url('/password/reset', $token) . '?email=' . urlencode($user->getEmailForPasswordReset());
        $data = array();
        $data["app_name"] = config('app.name');
        $data["subject"] = config('app.name') . ' - Reset your password!';
        $data["template_id"] = config('app.sendgrid_password_reset_template');
        $data["name"] = $user->name;
        $data["email"] = $user->email;
        $data["link"] = $link;
        self::sendEmail($user->email, $user->name, $data);
    }

    /**
     * Send new password email
     * NOT USED
     * @param User $user
     * @param string $token
     */
    public static function sendNewPasswordEmail(User $user, $token)
    {
        // Mail::send('auth.emails.new-password-patient', [
        //     'name' => $user->name,
        //     'token' => $token
        // ], function ($message) use ($user) {
        //     $message->to($user->getEmailForPasswordReset())->subject('APP Access new password!');
        // });
    }


    /**
     * Send OTP email
     *
     * @param User $user
     */
    public static function sendOtpEmail(User $user, $otp)
    {
        $data = array();
        $data["app_name"] = config('app.name');
        $data["subject"] = config('app.name') . ' - OTP For Login';
        $data["template_id"] = config('app.sendgrid_otp_login_template');
        $data["name"] = $user->name;
        $data["email"] = $user->email;
        $data["otp_content"] = $otp;
        self::sendEmail($user->email, $user->name, $data);
    }


    /**
     * Send verification email
     *
     * @param User $user
     */
    public static function sendUserVerificationEmail(TdtyUser $user, $slug)
    {
        $verificationLink = route('verify.email', ['slug' => $slug, 'code' => $user->verification_code]);

        $data = array();
        $data["app_name"] = config('app.name');
        $data["subject"] = config('app.name') . ' - Confirm your email address to begin';
        $data["template_id"] = config('app.sendgrid_signup_template');
        $data["name"] = $user->name;
        $data["email"] = $user->email;
        $data["link"] = $verificationLink;
        self::sendEmail($user->email, $user->name, $data);
    }
}
