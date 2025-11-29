@extends('tyro-login::layouts.auth')

@section('content')
<div class="auth-container {{ $layout }}">
    @if(in_array($layout, ['split-left', 'split-right']))
    <div class="background-panel" style="background-image: url('{{ $backgroundImage }}');">
        <div class="background-panel-content">
            <h1>Join Us Today!</h1>
            <p>Create your account and start your journey with us. It only takes a minute to get started.</p>
        </div>
    </div>
    @endif

    <div class="form-panel">
        <div class="form-card">
            <!-- Logo -->
            <div class="logo-container">
                @if($branding['logo'] ?? false)
                    <img src="{{ $branding['logo'] }}" alt="{{ $branding['app_name'] ?? config('app.name') }}">
                @else
                    <span class="app-name">{{ $branding['app_name'] ?? config('app.name', 'Laravel') }}</span>
                @endif
            </div>

            <!-- Header -->
            <div class="form-header">
                <h2>Create your account</h2>
                <p>Fill in your details to get started</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Registration Form -->
            <form method="POST" action="{{ route('tyro-login.register.submit') }}">
                @csrf

                <!-- Name Field -->
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input @error('name') is-invalid @enderror" 
                        value="{{ old('name') }}" 
                        required 
                        autocomplete="name" 
                        autofocus
                        placeholder="John Doe"
                    >
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input @error('email') is-invalid @enderror" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input @error('password') is-invalid @enderror" 
                        required 
                        autocomplete="new-password"
                        placeholder="Create a strong password"
                        minlength="{{ config('tyro-login.password.min_length', 8) }}"
                    >
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                        Minimum {{ config('tyro-login.password.min_length', 8) }} characters
                    </small>
                </div>

                <!-- Confirm Password Field -->
                @if($requirePasswordConfirmation ?? true)
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-input @error('password_confirmation') is-invalid @enderror" 
                        required 
                        autocomplete="new-password"
                        placeholder="Confirm your password"
                    >
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                <!-- Terms Agreement (Optional) -->
                <div class="form-group" style="margin-top: 1.5rem;">
                    <div class="checkbox-group">
                        <input 
                            type="checkbox" 
                            id="terms" 
                            name="terms" 
                            class="checkbox-input"
                            required
                        >
                        <label for="terms" class="checkbox-label">
                            I agree to the <a href="#" class="form-link">Terms of Service</a> and <a href="#" class="form-link">Privacy Policy</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <div class="form-footer">
                <p>
                    Already have an account? 
                    <a href="{{ route('tyro-login.login') }}" class="form-link">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
