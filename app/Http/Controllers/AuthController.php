<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $this->createToken($user);

        return $this->respondWithToken($token, 'Successfully registered', 201);
    }

    /**
     * Handle user login.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['success' => false, 'errors' => ['Invalid credentials']], 401);
        }

        $token = $this->createToken($request->user());

        return $this->respondWithToken($token, 'Login successful');
    }

    /**
     * Handle user logout.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Successfully logged out'], 200);
    }

    /**
     * Create a new token for the user.
     *
     * @param User $user
     * @return string
     */
    private function createToken(User $user): string
    {
        return $user->createToken('api-token')->plainTextToken;
    }

    /**
     * Respond with the token and a message.
     *
     * @param string $token
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    private function respondWithToken(string $token, string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'token' => $token,
        ], $statusCode);
    }
}
