<?php

namespace HasinHayder\TyroLogin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        // Check if user is locked out
        if ($this->isLockedOut($request)) {
            return redirect()->route('tyro-login.lockout');
        }

        return view('tyro-login::login', [
            'layout' => config('tyro-login.layout', 'centered'),
            'branding' => config('tyro-login.branding'),
            'backgroundImage' => config('tyro-login.background_image'),
            'features' => config('tyro-login.features'),
            'registrationEnabled' => config('tyro-login.registration.enabled', true),
        ]);
    }

    /**
     * Show the lockout page.
     */
    public function showLockout(Request $request): View|RedirectResponse
    {
        // Check if user is still locked out
        if (!$this->isLockedOut($request)) {
            // Clear the lockout cache and redirect to login
            $this->clearLockout($request);
            return redirect()->route('tyro-login.login');
        }

        $releaseTime = $this->getLockoutReleaseTime($request);
        $remainingMinutes = $releaseTime ? max(1, (int) ceil(($releaseTime - now()->timestamp) / 60)) : 0;

        $message = str_replace(
            ':minutes',
            (string) $remainingMinutes,
            config('tyro-login.lockout.message', 'Too many failed login attempts. Please try again in :minutes minutes.')
        );

        return view('tyro-login::lockout', [
            'layout' => config('tyro-login.layout', 'centered'),
            'branding' => config('tyro-login.branding'),
            'backgroundImage' => config('tyro-login.background_image'),
            'title' => config('tyro-login.lockout.title', 'Account Temporarily Locked'),
            'subtitle' => config('tyro-login.lockout.subtitle', 'For your security, we\'ve temporarily locked your account.'),
            'message' => $message,
            'remainingMinutes' => $remainingMinutes,
            'releaseTime' => $releaseTime,
        ]);
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request): RedirectResponse
    {
        // Check if user is locked out
        if ($this->isLockedOut($request)) {
            return redirect()->route('tyro-login.lockout');
        }

        $loginField = config('tyro-login.login_field', 'email');
        
        $credentials = $request->validate($this->getValidationRules($loginField));

        $remember = config('tyro-login.features.remember_me', true) 
            ? $request->boolean('remember') 
            : false;

        // Remove remember from credentials if it exists
        unset($credentials['remember']);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $this->clearLockout($request);

            return redirect()->intended(config('tyro-login.redirects.after_login', '/'));
        }

        $this->incrementLockoutAttempts($request);

        // Check if we should lock out the user now
        if ($this->shouldLockout($request)) {
            $this->lockoutUser($request);
            return redirect()->route('tyro-login.lockout');
        }

        // Build error message with remaining attempts if configured
        $errorMessage = __('auth.failed');
        
        if (config('tyro-login.lockout.enabled', true) && config('tyro-login.lockout.show_attempts_left', false)) {
            $attemptsLeft = $this->getRemainingAttempts($request);
            if ($attemptsLeft > 0) {
                $errorMessage .= ' ' . trans_choice(
                    '{1} :count attempt remaining.|[2,*] :count attempts remaining.',
                    $attemptsLeft,
                    ['count' => $attemptsLeft]
                );
            }
        }

        throw ValidationException::withMessages([
            $loginField => $errorMessage,
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
     * Get the lockout cache key for the request.
     */
    protected function lockoutKey(Request $request): string
    {
        return 'tyro-login:lockout:' . $request->ip();
    }

    /**
     * Get the lockout attempts cache key for the request.
     */
    protected function lockoutAttemptsKey(Request $request): string
    {
        return 'tyro-login:lockout-attempts:' . $request->ip();
    }

    /**
     * Check if the user is currently locked out.
     */
    protected function isLockedOut(Request $request): bool
    {
        if (!config('tyro-login.lockout.enabled', true)) {
            return false;
        }

        $releaseTime = $this->getLockoutReleaseTime($request);

        if (!$releaseTime) {
            return false;
        }

        // If lockout has expired, clear it
        if (now()->timestamp >= $releaseTime) {
            $this->clearLockout($request);
            return false;
        }

        return true;
    }

    /**
     * Get the lockout release timestamp.
     */
    protected function getLockoutReleaseTime(Request $request): ?int
    {
        return Cache::get($this->lockoutKey($request));
    }

    /**
     * Increment the lockout attempt counter.
     */
    protected function incrementLockoutAttempts(Request $request): void
    {
        if (!config('tyro-login.lockout.enabled', true)) {
            return;
        }

        $key = $this->lockoutAttemptsKey($request);
        $attempts = Cache::get($key, 0) + 1;
        
        // Store attempts for the lockout duration + some buffer time
        $durationMinutes = config('tyro-login.lockout.duration_minutes', 15);
        Cache::put($key, $attempts, now()->addMinutes($durationMinutes + 5));
    }

    /**
     * Check if the user should be locked out based on attempts.
     */
    protected function shouldLockout(Request $request): bool
    {
        if (!config('tyro-login.lockout.enabled', true)) {
            return false;
        }

        $attempts = Cache::get($this->lockoutAttemptsKey($request), 0);
        $maxAttempts = config('tyro-login.lockout.max_attempts', 5);

        return $attempts >= $maxAttempts;
    }

    /**
     * Get the remaining attempts before lockout.
     */
    protected function getRemainingAttempts(Request $request): int
    {
        $attempts = Cache::get($this->lockoutAttemptsKey($request), 0);
        $maxAttempts = config('tyro-login.lockout.max_attempts', 5);

        return max(0, $maxAttempts - $attempts);
    }

    /**
     * Lock out the user.
     */
    protected function lockoutUser(Request $request): void
    {
        $durationMinutes = config('tyro-login.lockout.duration_minutes', 15);
        $releaseTime = now()->addMinutes($durationMinutes)->timestamp;

        Cache::put($this->lockoutKey($request), $releaseTime, now()->addMinutes($durationMinutes));
    }

    /**
     * Clear the lockout for the user.
     */
    protected function clearLockout(Request $request): void
    {
        Cache::forget($this->lockoutKey($request));
        Cache::forget($this->lockoutAttemptsKey($request));
    }
}
