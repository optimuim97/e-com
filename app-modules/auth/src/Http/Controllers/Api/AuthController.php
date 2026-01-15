<?php

namespace Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\DTOs\BasicAuthLoginDTO;
use Modules\Auth\DTOs\ForgotPasswordDTO;
use Modules\Auth\DTOs\LoginDTO;
use Modules\Auth\DTOs\RegisterDTO;
use Modules\Auth\DTOs\ResetPasswordDTO;
use Modules\Auth\DTOs\UpdatePasswordDTO;
use Modules\Auth\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $dto = RegisterDTO::fromRequest($request);
        $result = $this->authService->register($dto);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $result['user'],
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'],
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $dto = LoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);

        return response()->json([
            'message' => 'Login successful',
            'user' => $result['user'],
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'],
        ]);
    }

    /**
     * Login with Basic Auth (for axios basic auth)
     */
    public function basicAuthLogin(Request $request)
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        if (!$username || !$password) {
            return response()->json([
                'message' => 'Basic authentication credentials required',
            ], 401);
        }

        $dto = BasicAuthLoginDTO::fromArray([
            'username' => $username,
            'password' => $password,
        ]);

        try {
            $result = $this->authService->basicAuthLogin($dto);

            return response()->json([
                'message' => 'Authentication successful',
                'user' => $result['user'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $this->authService->getAuthenticatedUser($request->user());

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $result = $this->authService->refreshToken($request->user());

        return response()->json([
            'message' => 'Token refreshed',
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'],
        ]);
    }

    /**
     * Request password reset
     */
    public function forgotPassword(Request $request)
    {
        $dto = ForgotPasswordDTO::fromRequest($request);
        $result = $this->authService->forgotPassword($dto);

        return response()->json($result);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request)
    {
        $dto = ResetPasswordDTO::fromRequest($request);
        $result = $this->authService->resetPassword($dto);

        return response()->json($result);
    }

    /**
     * Update authenticated user's password
     */
    public function updatePassword(Request $request)
    {
        $dto = UpdatePasswordDTO::fromRequest($request);
        $result = $this->authService->updatePassword($dto, $request->user());

        return response()->json($result);
    }
}
