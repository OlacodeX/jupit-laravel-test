<?php

use App\Models\User;
use App\Models\Verify;

it('can send reset password email', function () {
    $user = User::factory(['email_verified_at' => now()])->create();
    $response = $this->postJson('/api/v1/resetpassword', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200);
    expect($response->json()['message'])
        ->toBe("Email Sent");
});

it('can reset password', function () {
    $user = User::factory(['email_verified_at' => now()])->create();
    $verify = Verify::factory([
        'user_id' =>  $user->id,
        'email' =>  $user->email
    ])->create();
    $response = $this->postJson('/api/v1/verify', [
        'email' => $verify->email,
        'token' => $verify->token,
        'password' => 'Password690@',
        'password_confirmation' => 'Password690@',
    ]);

    $response->assertStatus(200);
    expect($response->json())
        ->toBe("Password reset successful.");
});
