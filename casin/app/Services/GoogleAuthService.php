<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class GoogleAuthService
{
    /**
     * Find or create a user from a Google Socialite user object.
     */
    public function findOrCreateUser(SocialiteUser $googleUser): User
    {
        $googleId = (string) $googleUser->getId();
        $email = Str::lower($googleUser->getEmail());

        $user = User::where('google_id', $googleId)
            ->orWhereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($user) {
            $user->forceFill([
                'google_id' => $googleId,
                'name' => $user->name ?: ($googleUser->getName() ?? Str::before($email, '@')),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            return $user;
        }

        return User::create([
            'name' => $googleUser->getName() ?? Str::before($email, '@'),
            'email' => $email,
            'google_id' => $googleId,
            'email_verified_at' => now(),
            'password' => Str::random(64),
        ]);
    }
}
