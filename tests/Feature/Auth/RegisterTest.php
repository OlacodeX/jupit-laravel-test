<?php

it('can register', function () {
    $email = fake()->unique()->safeEmail();
    $response = $this->postJson('/api/v1/register', [
        'email' => $email,
        'name' => fake()->name(),
        'password' => 'Password690@',
        'password_confirmation' => 'Password690@',
    ]);
    $response->assertStatus(201);
    expect($response->json()['data'])
        ->email
        ->toBe($email);
});

