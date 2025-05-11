<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Api\Support\Http\Controllers\ApiController;
use App\Enums\RolesEnum;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    public function __construct(protected OtpService $otpService)
    {
    }
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => RolesEnum::USER->value,
        ]);

        $this->otpService->send($user);
        DB::commit();

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

    public function user()
    {
        return $this->success([
            'user' => UserResource::make(auth()->user()),
        ]);
    }
}
