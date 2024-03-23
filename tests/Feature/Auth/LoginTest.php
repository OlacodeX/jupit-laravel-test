<?php

use App\Models\User;

it('can login', function () {
    $user = User::factory(['email_verified_at' => now()])->create();
    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'Password780@',
        'device_name' => 'PC',
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure(['token']);
});

it('can get logged in user', function () {
    $user = User::factory(['email_verified_at' => now()])->create();
    $response = $this->actingAs($user)->get('/api/v1/user');

    $response->assertStatus(200);
    expect($response->json())
        ->email
        ->toBe($user->email);
});

it('can not login with wrong credentials', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => 'notauser@example.com',
        'password' => 'Password780@',
        'device_name' => 'PC',
    ]);

    $response->assertStatus(422);
});
