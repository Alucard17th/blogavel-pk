<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $remember = false;

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $user = $request->user();
        if ($user === null) {
            $user = Auth::user();
        }

        if ($user === null) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tokenName = isset($credentials['device_name']) && $credentials['device_name'] !== ''
            ? $credentials['device_name']
            : 'api';

        $token = $user->createToken($tokenName);

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user?->id,
            'name' => $user?->name,
            'email' => $user?->email,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user !== null) {
            $user->currentAccessToken()?->delete();
        }

        return response()->json(['logged_out' => true]);
    }
}
