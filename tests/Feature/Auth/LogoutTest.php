<?php

use App\Models\User;

it('can logout', function () {
    $user = User::factory(['email_verified_at' => now()])->create();
    $response = $this->postJson('/api/v1/logout', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200);
    expect($response->json())->toEqual([]);
});
