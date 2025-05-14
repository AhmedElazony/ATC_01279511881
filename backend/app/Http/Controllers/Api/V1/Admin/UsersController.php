<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Api\Support\Http\Controllers\ApiController;
use App\Enums\RolesEnum;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UsersController extends ApiController
{
    public function index()
    {
        return $this->success(
            UserResource::paginate(
                User::query()->filter(request()->only(['q', 'role']))
                    ->latest()
                    ->paginate()
            )
        );
    }

    public function createAdmin(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'email_verified_at' => now(),
            'password' => bcrypt($validated['password']),
            'role' => RolesEnum::ADMIN,
        ]);

        return $this->success([
            'message' => 'Admin created successfully',
            'user' => UserResource::make($user),
        ]);
    }
}
