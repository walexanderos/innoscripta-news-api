<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthService
{
    public function registerUser(array $data): array
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
        ]);

        return [];
    }
    public function loginUser(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            return ['error' => 'Invalid login details'];
        }

        $token = request()->user()->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function logoutUser(User $user): void
    {
        $user->tokens()->delete();
    }
}
