<?php
namespace App\Services;

use App\Models\User;
use App\Models\UserPreference;

class UserService
{

    public function setPreferences(User $user, array $preferences)
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            $preferences
        );;
    }

    public function getPreferences(User $user)
    {
        return UserPreference::where('user_id', $user->id)->first();
    }
}
