<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 — Session Expired | {{ config('app.name') }}</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            background-color: #f3f4f6;
            color: #111827;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #111827;
                color: #f9fafb;
            }

            .card {
                background-color: #1f2937;
                border-color: #374151;
            }

            .card p {
                color: #9ca3af;
            }

            .step {
                background-color: #374151;
            }

            .step-num {
                background-color: #1d4ed8;
            }

            .btn-secondary {
                background-color: #374151;
                color: #e5e7eb;
            }

            .btn-secondary:hover {
                background-color: #4b5563;
            }

            .app-link {
                color: #60a5fa;
            }

            .error-num {
                color: #3b82f6;
            }

            .icon-wrap {
                background-color: rgba(234, 179, 8, 0.15);
            }
        }

        .error-num {
            font-size: 6rem;
            font-weight: 900;
            color: #1d4ed8;
            line-height: 1;
            letter-spacing: -0.05em;
            user-select: none;
            margin-bottom: 0.5rem;
        }

        .icon-wrap {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background-color: #fef9c3;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1rem 0 1.5rem;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
            max-width: 26rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #1d4ed8;
            color: #ffffff;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .btn-primary:hover {
            background-color: #1e40af;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #e5e7eb;
            color: #374151;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .card {
            background: #ffffff;
            border: 1px solid #f3f4f6;
            border-radius: 1rem;
            padding: 1.5rem;
            max-width: 26rem;
            width: 100%;
            margin-bottom: 2rem;
        }

        .card-title {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            background-color: #f9fafb;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
        }

        .step:last-child {
            margin-bottom: 0;
        }

        .step-num {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            background-color: #1d4ed8;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .step-text {
            font-size: 0.8rem;
            color: #6b7280;
            line-height: 1.5;
        }

        .step-text strong {
            color: #374151;
            font-weight: 600;
        }

        .app-link {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1d4ed8;
            text-decoration: none;
            transition: opacity 0.15s;
        }

        .app-link:hover {
            opacity: 0.8;
        }

        .timer-note {
            font-size: 0.75rem;
            color: #9ca3af;
            text-align: center;
            margin-top: -1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>

    {{-- Error code --}}
    <div class="error-num">419</div>

    {{-- Icon --}}
    <div class="icon-wrap">
        <svg width="40" height="40" fill="none" stroke="#ca8a04"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
            viewBox="0 0 24 24">
            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>

    {{-- Message --}}
    <h1>Session expired</h1>
    <p class="subtitle">
        Your session has timed out for security reasons.
        This usually happens when a page has been open for a long time
        without any activity. Your data has not been lost.
    </p>

    {{-- Actions --}}
    <div class="btn-row">
        <button onclick="window.location.reload()" class="btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                viewBox="0 0 24 24">
                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh and try again
        </button>
        <a href="/" class="btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                viewBox="0 0 24 24">
                <path d="M3 7h18M3 12h18M3 17h18" />
            </svg>
            Go to dashboard
        </a>
    </div>

    <p class="timer-note">
        Page refreshing automatically in
        <span id="countdown">10</span> seconds...
    </p>

    {{-- Step by step fix --}}
    <div class="card">
        <p class="card-title">How to fix this</p>

        <div class="step">
            <div class="step-num">1</div>
            <div class="step-text">
                <strong>Click "Refresh and try again"</strong> above.
                This reloads the page with a fresh CSRF token.
            </div>
        </div>

        <div class="step">
            <div class="step-num">2</div>
            <div class="step-text">
                <strong>Re-enter your form data</strong> if needed and
                submit again immediately.
            </div>
        </div>

        <div class="step">
            <div class="step-num">3</div>
            <div class="step-text">
                <strong>If this keeps happening,</strong> try clearing
                your browser cookies and logging in again.
            </div>
        </div>
    </div>

    {{-- App link --}}
    <a href="/" class="app-link">
        {{ config('app.name') }}
    </a>

    {{-- Auto countdown and refresh --}}
    <script>
        let seconds = 10;
        const el = document.getElementById('countdown');

        const timer = setInterval(function() {
            seconds--;
            if (el) el.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.reload();
            }
        }, 1000);
    </script>

</body>

</html>