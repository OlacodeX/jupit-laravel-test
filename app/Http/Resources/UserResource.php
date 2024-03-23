<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            /**
             * This is a temporal fix, since I am not able to make the api call for the notification system to send the email.
             * This (which is supposed to be sent by the mailing system) will be used for user verification
             */
            'temporal_data' => [
                'token' => $this->verification_token->token,
                'expires_at' => $this->verification_token->expires_at
            ],
        ];
    }
}
