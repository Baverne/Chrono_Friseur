<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    /**
     * The name of the OAuth provider.
     */
    private const PROVIDER_GITHUB = 'github';

    /**
     * The scopes to request from the OAuth provider.
     *
     * @var array<string>
     */
    private const GITHUB_SCOPES = ['read:user', 'email'];

    /**
     * The Socialite service.
     */
    private AuthenticationService $authenticationService;

    /**
     * Create a new controller instance.
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authenticationService->logoutUser($request);

        return to_route('login');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     */
    public function redirectToGithub(): RedirectResponse
    {
        return $this->authenticationService->redirectToProvider(
            self::PROVIDER_GITHUB,
            self::GITHUB_SCOPES
        );
    }

    /**
     * Handle the callback from GitHub OAuth.
     */
    public function handleGithubCallback(): RedirectResponse
    {
        $githubUser = $this->authenticationService
            ->getUserFromProvider(self::PROVIDER_GITHUB);

        $user = User::where('github_id', $githubUser->id)->first();

        if (! $user) {
            try {
                $user = $this->authenticationService
                    ->createOrUpdateUserFromGithub($githubUser);
            } catch (ValidationException $e) {
                return redirect()->route('login')
                    ->withErrors($e->validator);
            }
        }

        return $this->loginAndRedirect($user);
    }

    /**
     * Log in the user and redirect to the intended page.
     */
    private function loginAndRedirect(User $user): RedirectResponse
    {
        Auth::login($user);

        return redirect()->intended('/');
    }
}
