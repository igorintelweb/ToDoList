<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\AuthRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * @param AuthRegisterRequest $request
     * @return UserResource
     */
    public function __invoke(AuthRegisterRequest $request): UserResource
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        event(new Registered($user));
        $user->access_token = $user->createToken('api')->plainTextToken;

        return new UserResource($user);
    }
}
