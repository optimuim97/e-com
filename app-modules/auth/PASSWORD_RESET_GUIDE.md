# Password Reset and Update Guide

## Overview

This guide covers the password reset and update functionality in the authentication module. The system provides three main features:

1. **Forgot Password** - Users can request a password reset link
2. **Reset Password** - Users can reset their password using a token
3. **Update Password** - Authenticated users can change their password

## Table of Contents

- [Setup](#setup)
- [API Endpoints](#api-endpoints)
- [Password Reset Flow](#password-reset-flow)
- [Password Update Flow](#password-update-flow)
- [Security Features](#security-features)
- [Error Handling](#error-handling)
- [Testing](#testing)

## Setup

### 1. Run the Migration

Before using the password reset functionality, you need to run the migration to create the `password_reset_tokens` table:

```bash
php artisan migrate
```

This will create a table to store password reset tokens with the following structure:
- `email` (primary key)
- `token` (hashed)
- `created_at` (timestamp)

### 2. Email Configuration (Optional)

For production use, configure your email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## API Endpoints

### Public Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/forgot-password` | Request password reset | No |
| POST | `/api/reset-password` | Reset password with token | No |

### Protected Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/update-password` | Update current password | Yes (Bearer Token) |

## Password Reset Flow

### Step 1: Request Password Reset

**Endpoint:** `POST /api/forgot-password`

**Request Body:**
```json
{
  "email": "user@example.com"
}
```

**Success Response (200 OK):**
```json
{
  "message": "Password reset link has been sent to your email.",
  "token": "abc123..." // Only returned in development mode
}
```

**Validation Errors (422 Unprocessable Entity):**
```json
{
  "message": "The email field is required.",
  "errors": {
    "email": [
      "We could not find a user with that email address."
    ]
  }
}
```

**How it works:**
1. The system validates the email exists in the database
2. Any existing reset tokens for this email are deleted
3. A new 60-character random token is generated
4. The token is hashed and stored in the database
5. In production, an email would be sent with the reset link (currently returns token for testing)

### Step 2: Reset Password

**Endpoint:** `POST /api/reset-password`

**Request Body:**
```json
{
  "email": "user@example.com",
  "token": "abc123...",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Success Response (200 OK):**
```json
{
  "message": "Password has been reset successfully."
}
```

**Validation Errors (422 Unprocessable Entity):**
```json
{
  "message": "Validation failed",
  "errors": {
    "token": ["Invalid reset token."]
  }
}
```

**Possible Error Messages:**
- `"No password reset request found for this email."`
- `"Invalid reset token."`
- `"Reset token has expired. Please request a new one."` (tokens expire after 60 minutes)
- `"Password must be at least 8 characters."`
- `"Password confirmation does not match."`

**How it works:**
1. The system validates the email and token
2. Checks if the token matches the hashed token in the database
3. Verifies the token hasn't expired (60-minute expiration)
4. Updates the user's password with the new hashed password
5. Deletes the reset token from the database

## Password Update Flow

### Update Password (Authenticated Users)

**Endpoint:** `POST /api/update-password`

**Headers:**
```
Authorization: Bearer {access_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "current_password": "oldpassword123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

**Success Response (200 OK):**
```json
{
  "message": "Password updated successfully.",
  "access_token": "new_token_here",
  "token_type": "Bearer"
}
```

**Validation Errors (422 Unprocessable Entity):**
```json
{
  "message": "Validation failed",
  "errors": {
    "current_password": ["The current password is incorrect."]
  }
}
```

**How it works:**
1. The system verifies the user is authenticated
2. Validates the current password is correct
3. Updates the user's password with the new hashed password
4. Revokes all existing tokens for security
5. Issues a new access token

## Security Features

### Token Security
- **Hashing:** Reset tokens are hashed using bcrypt before storage
- **Expiration:** Tokens automatically expire after 60 minutes
- **Single Use:** Tokens are deleted after successful password reset
- **Uniqueness:** Only one reset token per email address at a time

### Password Requirements
- Minimum 8 characters
- Must be confirmed (password_confirmation field)
- Passwords are hashed using bcrypt before storage

### Token Revocation
- When updating password, all existing access tokens are revoked
- Forces re-authentication on all devices for security

## Error Handling

### Common HTTP Status Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 201 | Created (registration) |
| 401 | Unauthorized (invalid token or not authenticated) |
| 422 | Unprocessable Entity (validation errors) |
| 500 | Internal Server Error |

### Validation Error Response Format

```json
{
  "message": "Brief error description",
  "errors": {
    "field_name": [
      "Detailed error message"
    ]
  }
}
```

## Testing

### Testing with cURL

#### 1. Request Password Reset

```bash
curl -X POST http://localhost:8000/api/forgot-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com"
  }'
```

#### 2. Reset Password

```bash
curl -X POST http://localhost:8000/api/reset-password \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "token": "your_token_here",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }'
```

#### 3. Update Password (Authenticated)

```bash
curl -X POST http://localhost:8000/api/update-password \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_access_token" \
  -d '{
    "current_password": "currentpassword123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }'
```

### Testing with Postman

A Postman collection for the auth module is available at `postman_collection.json`. Import it and add the following requests to test password reset functionality.

### Unit Testing

Example test for password reset:

```php
<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can request password reset', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->postJson('/api/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'token']);

    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => 'test@example.com',
    ]);
});

test('user can reset password with valid token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    // Request reset
    $resetResponse = $this->postJson('/api/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $token = $resetResponse->json('token');

    // Reset password
    $response = $this->postJson('/api/reset-password', [
        'email' => 'test@example.com',
        'token' => $token,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Password has been reset successfully.']);

    // Verify new password works
    $user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $user->password));
});

test('authenticated user can update password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->postJson('/api/update-password', [
        'current_password' => 'oldpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ], [
        'Authorization' => "Bearer $token",
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['message', 'access_token', 'token_type']);

    $user->refresh();
    $this->assertTrue(Hash::check('newpassword123', $user->password));
});
```

## Best Practices

### For Development
1. The `forgotPassword()` method currently returns the token in the response for testing
2. In production, remove the token from the response and send it via email

### For Production
1. **Enable Email Sending:**
   - Uncomment the email sending line in `AuthService::forgotPassword()`
   - Create a `ResetPasswordMail` mailable class
   - Remove the `'token' => $token` from the response

2. **Frontend Integration:**
   - Create a password reset form that captures the token from the URL
   - Example reset URL: `https://yourapp.com/reset-password?token={token}&email={email}`

3. **Rate Limiting:**
   - Consider adding rate limiting to prevent abuse
   - Laravel's built-in rate limiting can be applied to routes

4. **Token Cleanup:**
   - Consider adding a scheduled job to clean up expired tokens
   - Example: Delete tokens older than 24 hours

### Example Email Implementation

Create a mailable class:

```bash
php artisan make:mail ResetPasswordMail
```

```php
<?php

namespace Modules\Auth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;
    public string $email;

    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function build()
    {
        $resetUrl = config('app.frontend_url') . '/reset-password?token=' . $this->token . '&email=' . $this->email;

        return $this->subject('Reset Your Password')
            ->view('emails.reset-password')
            ->with([
                'resetUrl' => $resetUrl,
            ]);
    }
}
```

Then update `AuthService::forgotPassword()`:

```php
use Modules\Auth\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;

// In forgotPassword method, replace the return with:
Mail::to($dto->email)->send(new ResetPasswordMail($token, $dto->email));

return [
    'message' => 'Password reset link has been sent to your email.',
];
```

## Troubleshooting

### Token Not Found
- Ensure the migration has been run
- Check that the email exists in the users table
- Verify the token hasn't expired (60 minutes)

### Token Mismatch
- Tokens are case-sensitive
- Ensure the token hasn't been used already
- Check that no spaces are added to the token

### Email Not Sent
- Verify email configuration in `.env`
- Check Laravel logs at `storage/logs/laravel.log`
- Test email configuration with `php artisan tinker` and `Mail::raw('Test', function($msg) { $msg->to('test@example.com'); });`

### Password Not Updating
- Verify the current password is correct
- Ensure the new password meets minimum requirements (8 characters)
- Check that password confirmation matches

## Support

For issues or questions:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Review the validation errors in the API response
3. Ensure all migrations have been run
4. Verify your `.env` configuration

## Related Documentation

- [Authentication Module README](README.md)
- [Postman Guide](POSTMAN_GUIDE.md)
- [Auth Module Installation](../../AUTH_MODULE_INSTALLATION.md)
- [Database Setup](../../DATABASE_SETUP.md)
