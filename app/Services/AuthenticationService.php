<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthenticationService
{
    const GITHUB_USER_VALIDATION = [
        'id' => 'required|integer|unique:users,github_id',
        'nickname' => 'required|string|unique:users,username|max:255',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users|max:255',
        'token' => 'required|string',
        'refreshToken' => 'required|string',
    ];

    const GITHUB_USER_MESSAGES = [
        'email' => [
            'required' => "Le compte Github n'a pas d'adresse email vérifiée.\n"
                .'Rendez-vous sur [https://github.com/settings/emails](https://github.com/settings/emails).',
        ],
    ];

    const GITHUB_USER_ATTRIBUTES = [
        'id' => 'identifiant Github',
        'nickname' => 'pseudo Github',
        'name' => 'nom Github',
        'email' => 'adresse email Github',
        'token' => 'jeton Github',
        'refreshToken' => 'jeton Github',
    ];

    public function redirectToProvider(string $driver, array $scopes = [])
    {
        return Socialite::driver($driver)
            ->scopes($scopes)
            ->redirect();
    }

    public function getUserFromProvider(string $driver)
    {
        return Socialite::driver($driver)->user();
    }

    /**
     * Create or update a user based on GitHub data.
     *
     * @throws ValidationException
     */
    public function createOrUpdateUserFromGithub($githubUser): User
    {
        $validator = Validator::make([
            'id' => $githubUser->id,
            'nickname' => $githubUser->nickname,
            'name' => $githubUser->name,
            'email' => $githubUser->email,
            'token' => $githubUser->token,
            'refreshToken' => $githubUser->refreshToken,
        ],
            self::GITHUB_USER_VALIDATION,
            self::GITHUB_USER_MESSAGES,
            self::GITHUB_USER_ATTRIBUTES);

        if ($validator->invalid()) {
            throw new ValidationException($validator, $validator->errors());
        }

        $validUser = $validator->safe();

        return User::updateOrCreate(
            ['github_id' => $validUser->id],
            [
                'username' => $validUser->nickname,
                'name' => $validUser->name,
                'email' => $validUser->email,
                'github_id' => $validUser->id,
                'github_token' => $validUser->token,
                'github_refresh_token' => $validUser->refreshToken,
            ]
        );
    }

    public function logoutUser(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
