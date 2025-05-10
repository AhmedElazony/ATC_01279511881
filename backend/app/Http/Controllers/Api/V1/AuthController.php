<?php

namespace App\Http\Controllers\Api\V1;

use Api\Support\Http\Controllers\ApiController;
use App\Enums\RolesEnum;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => RolesEnum::USER->value,
        ]);

        return $this->success([
            'user' => UserResource::make($user),
            'token' => $user->createToken('Api Token')->plainTextToken,
        ]);
    }

    public function login()
    {
        $validated = request()->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255',
        ]);

        if (!auth()->attempt($validated)) {
            return $this->error('Invalid Credentials', 401);
        }

        $user = auth()->user();

        return $this->success([
            'user' => UserResource::make($user),
            'token' => $user->createToken('Api Token')->plainTextToken,
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return $this->success('Logged out successfully');
    }
}
