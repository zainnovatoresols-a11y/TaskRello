<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Server Error | {{ config('app.name') }}</title>
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

            .code-block {
                background-color: #111827;
                border-color: #374151;
                color: #d1d5db;
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
                background-color: rgba(239, 68, 68, 0.15);
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
            background-color: #fee2e2;
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
            max-width: 24rem;
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
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
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
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
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
            max-width: 28rem;
            width: 100%;
            margin-bottom: 2rem;
        }

        .card-title {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            margin-bottom: 0.75rem;
        }

        .card p {
            font-size: 0.8rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .card p:last-child {
            margin-bottom: 0;
        }

        .code-block {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-family: ui-monospace, monospace;
            font-size: 0.75rem;
            color: #374151;
            margin-top: 0.75rem;
            word-break: break-all;
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
    </style>
</head>

<body>

    {{-- Error code --}}
    <div class="error-num">500</div>

    {{-- Icon --}}
    <div class="icon-wrap">
        <svg width="40" height="40" fill="none" stroke="#ef4444"
            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
            viewBox="0 0 24 24">
            <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
        </svg>
    </div>

    {{-- Message --}}
    <h1>Something went wrong</h1>
    <p class="subtitle">
        The server encountered an unexpected error and could not
        complete your request. This is not your fault.
        Please try again in a moment.
    </p>

    {{-- Actions --}}
    <div class="btn-row">
        <a href="javascript:history.back()" class="btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Go back
        </a>
        <a href="/" class="btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                viewBox="0 0 24 24">
                <path d="M3 7h18M3 12h18M3 17h18" />
            </svg>
            Go to dashboard
        </a>
    </div>

    {{-- What to try --}}
    <div class="card">
        <p class="card-title">What you can try</p>
        <p>Refresh the page — the error may be temporary.</p>
        <p>Clear your browser cache and try again.</p>
        <p>
            If the problem persists, contact the administrator
            and mention what you were doing when this happened.
        </p>
        @if(config('app.debug') && isset($exception))
        <div class="code-block">
            <strong>Debug info:</strong><br>
            {{ $exception->getMessage() }}
        </div>
        @endif
    </div>

    {{-- Refresh button --}}
    <button onclick="window.location.reload()"
        class="btn-secondary"
        style="border: none; cursor: pointer; margin-bottom: 2rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            viewBox="0 0 24 24">
            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Refresh page
    </button>

    {{-- App link --}}
    <a href="/" class="app-link">
        {{ config('app.name') }}
    </a>

</body>

</html>