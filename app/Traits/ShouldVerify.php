<?php

namespace App\Traits;

use App\Models\Verify;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait ShouldVerify
{
    private function generateVerifier(): Verify
    {
        $verifyUser = Verify::create([
            'user_id' => $this->id,
            'token' => Str::random(60),
            'email' => $this->email,
            'expires_at' => now()->addMinutes(180)
        ]);

        return $verifyUser;
    }

    public function sendVerificationEmail()
    {
        $verifyUser = $this->generateVerifier();

        try {
            // call notification microservice to send mail
        } catch (Exception $e) {
            //catch exception and do something
        }
        
        // This is a temporal fix, since I am not able to make the api call for the notification system to send the email.
        return $verifyUser;
    }

    
    public function sendPasswordResetEmail()
    {
        $verifyUser = $this->generateVerifier();

        try {
            // call notification microservice to send mail
        } catch (Exception $e) {
            //catch exception and do something
        }
        
        // This is a temporal fix, since I am not able to make the api call for the notification system to send the email.
        return $verifyUser;
    }

    public function verify(Verify $verifier, ?String $password)
    {
        $user = $verifier->user;
        $password ? $user->password = Hash::make($password) : null;
        !$user->email_verified_at ? $user->email_verified_at = now() : null;
        $user->save();
        $verifier->delete();
        return ;
    }
}
