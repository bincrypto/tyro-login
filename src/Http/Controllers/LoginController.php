<?php

namespace HasinHayder\TyroLogin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('tyro-login::login', [
            'layout' => config('tyro-login.layout', 'centered'),
            'branding' => config('tyro-login.branding'),
            'backgroundImage' => config('tyro-login.background_image'),
            'features' => config('tyro-login.features'),
            'registrationEnabled' => config('tyro-login.registration.enabled', true),
        ]);
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        $loginField = config('tyro-login.login_field', 'email');
        
        $credentials = $request->validate($this->getValidationRules($loginField));

        $remember = config('tyro-login.features.remember_me', true) 
            ? $request->boolean('remember') 
            : false;

        // Remove remember from credentials if it exists
        unset($credentials['remember']);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            RateLimiter::clear($this->throttleKey($request));

            return redirect()->intended(config('tyro-login.redirects.after_login', '/dashboard'));
        }

        $this->incrementRateLimiter($request);

        throw ValidationException::withMessages([
            $loginField => __('auth.failed'),
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(config('tyro-login.redirects.after_logout', '/login'));
    }

    /**
     * Get validation rules based on login field.
     */
    protected function getValidationRules(string $loginField): array
    {
        $rules = [
            'password' => ['required', 'string'],
        ];

        if ($loginField === 'email') {
            $rules['email'] = ['required', 'string', 'email'];
        } elseif ($loginField === 'username') {
            $rules['username'] = ['required', 'string'];
        } else {
            // 'both' - accept either email or username
            $rules['login'] = ['required', 'string'];
        }

        return $rules;
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!config('tyro-login.rate_limiting.enabled', true)) {
            return;
        }

        $maxAttempts = config('tyro-login.rate_limiting.max_attempts', 5);

        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), $maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Increment the rate limiter.
     */
    protected function incrementRateLimiter(Request $request): void
    {
        if (!config('tyro-login.rate_limiting.enabled', true)) {
            return;
        }

        $decayMinutes = config('tyro-login.rate_limiting.decay_minutes', 1);

        RateLimiter::hit($this->throttleKey($request), $decayMinutes * 60);
    }

    /**
     * Get the rate limiting throttle key.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email', $request->input('username', ''))) . '|' . $request->ip());
    }
}
