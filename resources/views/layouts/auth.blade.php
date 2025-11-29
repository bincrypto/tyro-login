<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $branding['app_name'] ?? config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <style>
        :root {
            --primary-color: {{ $branding['primary_color'] ?? '#4f46e5' }};
            --primary-hover-color: {{ $branding['primary_hover_color'] ?? '#4338ca' }};
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f9fafb;
            min-height: 100vh;
            line-height: 1.6;
            color: #1f2937;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Layout Styles */
        .auth-container {
            min-height: 100vh;
            display: flex;
        }

        .auth-container.centered {
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .auth-container.split-left,
        .auth-container.split-right {
            flex-direction: row;
        }

        .auth-container.split-left {
            flex-direction: row;
        }

        .auth-container.split-right {
            flex-direction: row-reverse;
        }

        /* Background Panel (for split layouts) */
        .background-panel {
            display: none;
            flex: 1;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        .background-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.85) 0%, rgba(99, 102, 241, 0.75) 100%);
        }

        .background-panel-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            color: white;
            height: 100%;
        }

        .background-panel-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .background-panel-content p {
            font-size: 1.125rem;
            opacity: 0.9;
            max-width: 28rem;
        }

        .auth-container.split-left .background-panel,
        .auth-container.split-right .background-panel {
            display: flex;
        }

        /* Form Panel */
        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background-color: #ffffff;
        }

        .auth-container.centered .form-panel {
            background-color: transparent;
            padding: 0;
        }

        .auth-container.split-left .form-panel,
        .auth-container.split-right .form-panel {
            flex: 1;
            max-width: 50%;
        }

        /* Form Card */
        .form-card {
            width: 100%;
            max-width: 28rem;
            background: #ffffff;
            border-radius: 1rem;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .auth-container.split-left .form-card,
        .auth-container.split-right .form-card {
            box-shadow: none;
        }

        /* Logo */
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container img {
            height: {{ $branding['logo_height'] ?? '48px' }};
            width: auto;
        }

        .logo-container .app-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Form Header */
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #6b7280;
            font-size: 0.9375rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: all 0.15s ease;
            background-color: #ffffff;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-input.is-invalid {
            border-color: #ef4444;
        }

        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        /* Checkbox */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-input {
            width: 1rem;
            height: 1rem;
            border-radius: 0.25rem;
            border: 1px solid #d1d5db;
            accent-color: var(--primary-color);
        }

        .checkbox-label {
            font-size: 0.875rem;
            color: #4b5563;
        }

        /* Form Options Row */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        /* Links */
        .form-link {
            font-size: 0.875rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s ease;
        }

        .form-link:hover {
            color: var(--primary-hover-color);
            text-decoration: underline;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.875rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 600;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover-color);
            transform: translateY(-1px);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Error Messages */
        .error-message {
            color: #ef4444;
            font-size: 0.8125rem;
            margin-top: 0.375rem;
        }

        .error-list {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .error-list ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .error-list li {
            color: #dc2626;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .error-list li:last-child {
            margin-bottom: 0;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }

        .divider span {
            padding: 0 1rem;
        }

        /* Form Footer */
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .form-footer p {
            color: #6b7280;
            font-size: 0.9375rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .auth-container.split-left .background-panel,
            .auth-container.split-right .background-panel {
                display: none;
            }

            .auth-container.split-left .form-panel,
            .auth-container.split-right .form-panel {
                max-width: 100%;
            }

            .auth-container.split-left,
            .auth-container.split-right {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .auth-container.split-left .form-panel,
            .auth-container.split-right .form-panel {
                background-color: transparent;
            }

            .auth-container.split-left .form-card,
            .auth-container.split-right .form-card {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            }
        }

        @media (max-width: 480px) {
            .form-card {
                padding: 1.5rem;
                border-radius: 0.75rem;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }

        /* Loading State */
        .btn.loading {
            position: relative;
            color: transparent;
        }

        .btn.loading::after {
            content: '';
            position: absolute;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    @yield('content')

    <script>
        // Form validation enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.classList.contains('loading')) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                    }
                });
            });

            // Real-time validation feedback
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value && this.checkValidity()) {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.checkValidity()) {
                        this.classList.remove('is-invalid');
                        const errorEl = this.parentNode.querySelector('.error-message');
                        if (errorEl) {
                            errorEl.remove();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
