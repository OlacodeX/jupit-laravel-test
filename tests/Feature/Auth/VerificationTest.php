<?php

use App\Models\User;
use App\Models\Verify;

it('can verify account', function () {
    $user = User::factory()->create();
    $verify = Verify::factory([
        'user_id' =>  $user->id,
        'email' =>  $user->email
    ])->create();
    $response = $this->postJson('/api/v1/verify', [
        'email' => $verify->email,
        'token' => $verify->token
    ]);

    $response->assertStatus(200);
});


it('can resend verification email', function () {
    $user = User::factory()->create();
    $response = $this->postJson('/api/v1/resendVerificationMail', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200);
    expect($response->json()['message'])
        ->toBe("Verification Email Resent.");
});