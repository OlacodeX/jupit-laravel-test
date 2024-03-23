<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerificationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Verify;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    
    public function register(RegisterRequest $request)
    {
        $validatedInput = $request->validated();

        $user = User::create($validatedInput);
        $user->sendVerificationEmail();

        return new UserResource($user, Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $validatedInput = $request->validated();

        $user = User::where('email', $validatedInput['email'])->first();

        if (!$user->email_verified_at) {
            $user->sendVerificationEmail();
            return response()->json('You need to verify your account, kindly check your email for instructions.', Response::HTTP_BAD_REQUEST);
        }

        if (!$user || !Hash::check($validatedInput['password'], $user->password)) {
            return response()->json('The provided credentials are incorrect.', Response::HTTP_BAD_REQUEST);
        }

        $token = $user->createToken($request->device_name, ['*'], now()->addDays(5));
        
        return response()->json([
            'token' => $token->plainTextToken
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request)
    {
        $validatedInput = $request->validate([
            'email' => ['required','string', 'email']
        ]);
        $user = User::where('email', $validatedInput['email'])->first();
        $user->tokens()->delete();
        return response()->json([], Response::HTTP_OK);
    }

    public function resetPassword(Request $request)
    {

        $validatedInput = $request->validate([
            'email' => ['required','string', 'email', 'exists:users,email']
        ]);
        $user = User::where('email', $validatedInput['email'])->first();
        $data = $user->sendPasswordResetEmail();

        return response()->json([
            "message" => "Email Sent",
            "temporal_data" => $data,
        ], Response::HTTP_OK);
    }

    public function verify(VerificationRequest $request)
    {
        $validatedInput = $request->validated();
        $verify = Verify::where(['token' => $validatedInput['token'], 'email' => $validatedInput['email']])->first();

        if (!$verify) {
            return response()->json("Invalid token", Response::HTTP_BAD_REQUEST);
        }

        if ($verify->expires_at->isPast()) {
            return response()->json("Link has expired", Response::HTTP_BAD_REQUEST);
        }

        $verify->user->verify($verify, $request->password);

        return $request->has('password') ? response()->json("Password reset successful.", Response::HTTP_OK) : response()->json("Email Address Verified.", Response::HTTP_OK);
    }

    public function resendVerificationEmail(Request $request)
    {
        
        $validatedInput = $request->validate([
            'email' => ['required','string', 'email', 'exists:users,email']
        ]);

        try{
            $user = User::where('email', $validatedInput['email'])->firstorfail();

        } catch (ModelNotFoundException $exception) {
            return response()->json("Email not found", Response::HTTP_NOT_FOUND);
        }
        
        if(method_exists($user, 'sendVerificationEmail') && empty($user->email_verified_at))
        {
            $data = $user->sendVerificationEmail();
            return response()->json([
                "message" => "Verification Email Resent.",
                "temporal_data" => $data,
            ], Response::HTTP_OK);
        }

        return response()->json("Email Already Verified.", Response::HTTP_OK);

    }
}
