<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticatedRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): UserResource
    {
        $user = User::create($request->validated());
        Auth::login($user);
        $request->session()->regenerate();
        return new UserResource($user);
    }

    public function login(LoginRequest $request): UserResource
    {
        if (! Auth::attempt($request->validated())) {
            throw ValidationException::withMessages(['email' => ['Le credenziali non sono corrette.']]);
        }
        $request->session()->regenerate();
        return new UserResource($request->user());
    }

    public function me(AuthenticatedRequest $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function logout(AuthenticatedRequest $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(null, 204);
    }
}
