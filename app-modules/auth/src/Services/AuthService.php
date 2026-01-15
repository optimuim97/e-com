<?php

namespace Modules\Auth\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Auth\DTOs\BasicAuthLoginDTO;
use Modules\Auth\DTOs\ForgotPasswordDTO;
use Modules\Auth\DTOs\LoginDTO;
use Modules\Auth\DTOs\RegisterDTO;
use Modules\Auth\DTOs\ResetPasswordDTO;
use Modules\Auth\DTOs\UpdatePasswordDTO;

class AuthService
{
    /**
     * Register a new user.
     *
     * @param RegisterDTO $dto
     * @return array
     */
    public function register(RegisterDTO $dto): array
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Login a user with email and password.
     *
     * @param LoginDTO $dto
     * @return array
     * @throws ValidationException
     */
    public function login(LoginDTO $dto): array
    {
        $credentials = [
            'email' => $dto->email,
            'password' => $dto->password,
        ];

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $dto->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Login a user with basic authentication.
     *
     * @param BasicAuthLoginDTO $dto
     * @return array
     * @throws ValidationException
     */
    public function basicAuthLogin(BasicAuthLoginDTO $dto): array
    {
        // Try to find user by email or name
        $user = User::where('email', $dto->username)
            ->orWhere('name', $dto->username)
            ->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Invalid credentials'],
            ]);
        }

        Auth::login($user);

        return [
            'user' => $user,
        ];
    }

    /**
     * Logout a user by deleting their current access token.
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Refresh a user's access token.
     *
     * @param User $user
     * @return array
     */
    public function refreshToken(User $user): array
    {
        $user->currentAccessToken()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Get the authenticated user.
     *
     * @param User $user
     * @return User
     */
    public function getAuthenticatedUser(User $user): User
    {
        return $user;
    }

    /**
     * Send a password reset link to the user.
     *
     * @param ForgotPasswordDTO $dto
     * @return array
     */
    public function forgotPassword(ForgotPasswordDTO $dto): array
    {
        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')->where('email', $dto->email)->delete();

        // Create a new reset token
        $token = Str::random(60);
        
        DB::table('password_reset_tokens')->insert([
            'email' => $dto->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // In a real application, you would send an email here
        // For now, we'll return the token for testing purposes
        // Mail::to($dto->email)->send(new ResetPasswordMail($token));

        return [
            'message' => 'Password reset link has been sent to your email.',
            'token' => $token, // Remove this in production
        ];
    }

    /**
     * Reset the user's password.
     *
     * @param ResetPasswordDTO $dto
     * @return array
     * @throws ValidationException
     */
    public function resetPassword(ResetPasswordDTO $dto): array
    {
        // Retrieve the reset token from the database
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $dto->email)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'email' => ['No password reset request found for this email.'],
            ]);
        }

        // Check if token matches
        if (!Hash::check($dto->token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['Invalid reset token.'],
            ]);
        }

        // Check if token is expired (60 minutes)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $dto->email)->delete();
            throw ValidationException::withMessages([
                'token' => ['Reset token has expired. Please request a new one.'],
            ]);
        }

        // Update the user's password
        $user = User::where('email', $dto->email)->first();
        $user->password = Hash::make($dto->password);
        $user->save();

        // Delete the reset token
        DB::table('password_reset_tokens')->where('email', $dto->email)->delete();

        return [
            'message' => 'Password has been reset successfully.',
        ];
    }

    /**
     * Update the authenticated user's password.
     *
     * @param UpdatePasswordDTO $dto
     * @param User $user
     * @return array
     * @throws ValidationException
     */
    public function updatePassword(UpdatePasswordDTO $dto, User $user): array
    {
        // Verify current password
        if (!Hash::check($dto->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        // Update the password
        $user->password = Hash::make($dto->password);
        $user->save();

        // Optionally revoke all existing tokens
        $user->tokens()->delete();

        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Password updated successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
