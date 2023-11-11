<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @param Request $request
     * @return UserResource
     * @throws ValidationException
     */
    public function __invoke(Request $request): UserResource
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|max:191|alpha_num'
        ]);

        $user = User::whereEmail($validated['email'])->first();
        if ($user && Hash::check($validated['password'], $user->password)) {
            $user->access_token = $user->createToken('api')->plainTextToken;
            return new UserResource($user);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
}
