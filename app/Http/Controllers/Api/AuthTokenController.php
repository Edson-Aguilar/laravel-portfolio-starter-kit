<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        abort_unless(config('starter.modules.api'), 404);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:80'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no son correctas.',
            ]);
        }

        $abilities = ['user:read'];

        if ($user->hasAnyRole(['admin', 'editor']) && config('starter.modules.projects')) {
            $abilities[] = 'projects:read';
        }

        $token = $user->createToken($credentials['device_name'] ?? 'starter-api', $abilities);

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token->plainTextToken,
            'abilities' => $abilities,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        abort_unless(config('starter.modules.api'), 404);

        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Sesión API cerrada correctamente.',
        ]);
    }
}
