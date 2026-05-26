<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&family=Cormorant:ital,wght@0,300;0,400;1,300;1,400&display=swap');

    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px #0e0e14 inset !important;
        box-shadow: 0 0 0 1000px #0e0e14 inset !important;
        -webkit-text-fill-color: #e8e8f0 !important;
        caret-color: #e8e8f0;
        transition: background-color 9999s ease-in-out 0s;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        --bg-deep: #08080f;
        --bg-card: #0e0e14;
        --bg-panel: #0b0b11;
        --bg-input: #13131a;
        --border: rgba(255, 255, 255, 0.08);
        --border-hover: rgba(255, 255, 255, 0.16);
        --border-focus: rgba(99, 102, 241, 0.6);
        --text-primary: #eeeef4;
        --text-secondary: #8888a8;
        --text-muted: #55556a;
        --accent: #6366f1;
        --accent-glow: rgba(99, 102, 241, 0.18);
        --accent-soft: rgba(99, 102, 241, 0.08);
        --error: #f87171;
        --error-bg: rgba(248, 113, 113, 0.08);
        --success: #34d399;
    }

    body {
        background: var(--bg-deep) !important;
    }

    .min-h-screen {
        background: var(--bg-deep) !important;
    }

    .lx-shell {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: stretch;
        background: var(--bg-deep);
        font-family: 'DM Sans', sans-serif;
        overflow: hidden;
        height: 100vh;
        width: 100vw;
    }

    .lx-orb {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        filter: blur(80px);
    }

    .lx-orb-1 {
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
        top: -200px;
        right: -100px;
    }

    .lx-orb-2 {
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.08) 0%, transparent 70%);
        bottom: -100px;
        left: 100px;
    }

    .lx-orb-3 {
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(20, 184, 166, 0.06) 0%, transparent 70%);
        top: 50%;
        left: 25%;
    }

    .lx-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
        background-size: 48px 48px;
        pointer-events: none;
    }

    /* Left panel */
    .lx-left {
        display: none;
        position: relative;
        flex-direction: column;
        justify-content: space-between;
        padding: 52px 56px;
        width: 44%;
        flex-shrink: 0;
        border-right: 1px solid var(--border);
        overflow: hidden;
    }

    @media (min-width: 960px) {
        .lx-left {
            display: flex;
        }
    }

    .lx-left-inner {
        position: relative;
        z-index: 2;
        padding-top: 48px;
    }

    .lx-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 28px;
    }

    .lx-brand-icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        background: var(--accent-soft);
        border: 1px solid rgba(99, 102, 241, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lx-brand-name {
        font-size: 0.82rem;
        font-weight: 500;
        color: var(--text-secondary);
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .lx-headline h2 {
        font-family: 'Cormorant', serif;
        font-size: clamp(2.4rem, 3.5vw, 3.2rem);
        font-weight: 300;
        color: var(--text-primary);
        line-height: 1.18;
        letter-spacing: -0.01em;
        margin-bottom: 24px;
    }

    .lx-headline h2 em {
        font-style: italic;
        color: var(--accent);
        opacity: 0.85;
    }

    .lx-headline p {
        font-size: 0.875rem;
        color: var(--text-secondary);
        line-height: 1.75;
        max-width: 320px;
        font-weight: 300;
    }

    .lx-features {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 48px;
    }

    .lx-feature {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lx-feature-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--accent);
        opacity: 1;
        flex-shrink: 0;
        margin-top: 5px;
    }

    .lx-feature span {
        font-size: 0.82rem;
        color: var(--text-secondary);
        letter-spacing: 0.01em;
        line-height: 1.5;
    }

    .lx-left-footer {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lx-avatars {
        display: flex;
    }

    .lx-av {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid var(--bg-panel);
        margin-left: -7px;
        flex-shrink: 0;
    }

    .lx-av:first-child {
        margin-left: 0;
        background: linear-gradient(135deg, #f093fb, #f5576c);
    }

    .lx-av:nth-child(2) {
        background: linear-gradient(135deg, #4facfe, #00f2fe);
    }

    .lx-av:nth-child(3) {
        background: linear-gradient(135deg, #43e97b, #38f9d7);
    }

    .lx-left-footer p {
        font-size: 0.75rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Right panel */
    .lx-right {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 48px 48px;
        position: relative;
        z-index: 2;
        overflow-y: auto;
    }

    .lx-form-wrap {
        width: 100%;
        max-width: 420px;
    }

    .lx-topnav {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 52px;
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .lx-topnav a {
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.15s;
    }

    .lx-topnav a:hover {
        opacity: 0.75;
    }

    .lx-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.7rem;
        font-weight: 500;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: 16px;
    }

    .lx-tag::before {
        content: '';
        display: inline-block;
        width: 18px;
        height: 1.5px;
        background: var(--accent);
    }

    .lx-title {
        font-family: 'Cormorant', serif;
        font-size: clamp(2rem, 4vw, 2.6rem);
        font-weight: 300;
        color: var(--text-primary);
        letter-spacing: -0.02em;
        line-height: 1.1;
        margin-bottom: 8px;
    }

    .lx-subtitle {
        font-size: 0.855rem;
        color: var(--text-secondary);
        margin-bottom: 40px;
        line-height: 1.6;
    }

    .lx-status {
        margin-bottom: 20px;
        padding: 12px 16px;
        background: rgba(52, 211, 153, 0.08);
        border: 1px solid rgba(52, 211, 153, 0.18);
        border-radius: 10px;
        font-size: 0.82rem;
        color: var(--success);
    }

    .lx-field {
        margin-bottom: 18px;
    }

    .lx-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 500;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 10px;
    }

    .lx-input-wrap {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--bg-input);
        border: 1px solid var(--border);
        border-radius: 10px;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }

    .lx-input-wrap:hover {
        border-color: var(--border-hover);
    }

    .lx-input-wrap:focus-within {
        border-color: var(--border-focus);
        box-shadow: 0 0 0 3px var(--accent-glow);
        background: #14141c;
    }

    .lx-input-wrap:focus-within .lx-icon {
        color: var(--accent);
        transition: color 0.2s;
    }

    .lx-input-wrap.has-error {
        border-color: rgba(248, 113, 113, 0.5);
        box-shadow: 0 0 0 3px var(--error-bg);
    }

    .lx-icon {
        display: flex;
        align-items: center;
        padding: 0 12px 0 15px;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .lx-input {
        flex: 1;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        padding: 14px 12px 14px 0 !important;
        font-size: 1rem !important;
        font-family: 'DM Sans', sans-serif !important;
        color: var(--text-primary) !important;
        letter-spacing: 0.01em;
        width: 100%;
    }

   .lx-input::placeholder {
    color: #44445a !important;
}

    .lx-eye {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0 14px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        transition: color 0.15s;
    }

    .lx-eye:hover {
        color: var(--text-primary);
    }

    .lx-blade-error,
    .js-error {
        display: block;
        font-size: 0.75rem;
        color: var(--error);
        margin-top: 6px;
        padding-left: 4px;
        font-family: 'DM Sans', sans-serif;
    }

    .lx-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px 0 28px;
    }

    .lx-remember {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--text-secondary);
        cursor: pointer;
        user-select: none;
    }

    .lx-remember input[type="checkbox"] {
        appearance: none;
        width: 15px;
        height: 15px;
        border: 1px solid var(--border-hover);
        border-radius: 4px;
        background: transparent;
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
        transition: border-color 0.15s, background 0.15s;
    }

    .lx-remember input[type="checkbox"]:checked {
        background: var(--accent);
        border-color: var(--accent);
    }

    .lx-remember input[type="checkbox"]:checked::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 4px;
        width: 4px;
        height: 7px;
        border: 1.5px solid #fff;
        border-top: none;
        border-left: none;
        transform: rotate(43deg);
    }

    .lx-forgot {
        font-size: 0.82rem;
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.15s;
    }

    .lx-forgot:hover {
        color: var(--text-primary);
    }

    .lx-btn {
        width: 100%;
        padding: 14px 24px;
        background: var(--accent);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 500;
        letter-spacing: 0.03em;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 24px rgba(99, 102, 241, 0.28);
    }

    .lx-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, transparent 60%);
    }

    .lx-btn:hover {
        opacity: 0.92;
        transform: translateY(-1px);
        box-shadow: 0 8px 32px rgba(99, 102, 241, 0.38);
    }

    .lx-btn:active {
        transform: translateY(0);
    }

    .lx-btn:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .lx-div {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 28px 0 20px;
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .lx-div::before,
    .lx-div::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    .lx-reg {
        text-align: center;
        font-size: 0.82rem;
        color: var(--text-muted);
    }

    .lx-reg a {
        color: var(--text-secondary);
        font-weight: 500;
        text-decoration: none;
        border-bottom: 1px solid var(--border-hover);
        padding-bottom: 1px;
        transition: color 0.15s, border-color 0.15s;
    }

    .lx-reg a:hover {
        color: var(--text-primary);
        border-color: var(--text-primary);
    }

    @keyframes lx-up {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .a1 {
        animation: lx-up 0.5s 0.05s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .a2 {
        animation: lx-up 0.5s 0.12s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .a3 {
        animation: lx-up 0.5s 0.18s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .a4 {
        animation: lx-up 0.5s 0.24s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .a5 {
        animation: lx-up 0.5s 0.30s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .a6 {
        animation: lx-up 0.5s 0.36s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .a7 {
        animation: lx-up 0.5s 0.42s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation: none !important;
            transition-duration: 0.01ms !important;
        }
    }

    @media (max-width: 959px) {
        .lx-right {
            padding: 48px 28px;
            justify-content: center;
        }

        .lx-form-wrap {
            max-width: 100%;
        }

        .lx-title {
            font-size: 2rem;
            margin-bottom: 6px;
        }

        .lx-subtitle {
            font-size: 0.85rem;
            margin-bottom: 28px;
        }

        .lx-input {
            padding: 13px 10px 13px 0 !important;
            font-size: 1rem !important;
        }

        .lx-btn {
            padding: 15px 24px;
            font-size: 0.9rem;
        }

        .lx-meta {
            flex-direction: row;
            flex-wrap: wrap;
            gap: 8px;
        }
    }

    .lx-input {
        font-size: 1rem !important;
        -webkit-text-size-adjust: 100%;
    }
</style>

<x-guest-layout>
    <div class="lx-shell">
        <div class="lx-orb lx-orb-1"></div>
        <div class="lx-orb lx-orb-2"></div>
        <div class="lx-orb lx-orb-3"></div>


        <div class="lx-left">
            <div class="lx-left-inner">
                <div class="lx-brand">
                    <div class="lx-brand-icon">
                        <svg width="16" height="16" viewBox="0 0 18 18" fill="none">
                            <path d="M9 1L16 5.5V12.5L9 17L2 12.5V5.5L9 1Z" stroke="#6366f1" stroke-width="1.4" stroke-linejoin="round" />
                            <path d="M9 6L12 8V11L9 13L6 11V8L9 6Z" fill="#6366f1" fill-opacity="0.3" stroke="#6366f1" stroke-width="0.8" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="lx-brand-name">{{ config('app.name', 'Platform') }}</span>
                </div>

                <div class="lx-headline">
                    <h2>Welcome<br>back, <em>again.</em></h2>
                    <p>Sign in to pick up right where you left off. Your workspace is waiting.</p>
                </div>

                <div class="lx-features">
                    <div class="lx-feature">
                        <div class="lx-feature-dot"></div>
                        <span>Boards, lists &amp; cards — all in one place</span>
                    </div>
                    <div class="lx-feature">
                        <div class="lx-feature-dot"></div>
                        <span>Real-time collaboration with your team</span>
                    </div>
                    <div class="lx-feature">
                        <div class="lx-feature-dot"></div>
                        <span>Track progress across every project</span>
                    </div>
                    <div class="lx-feature">
                        <div class="lx-feature-dot"></div>
                        <span>Deadlines, priorities &amp; assignments</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="lx-right">
            <div class="lx-form-wrap">


                <div class="a2">

                    <h1 class="lx-title">Sign in</h1>
                    <p class="lx-subtitle">Enter your credentials to access your dashboard.</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <x-auth-session-status class="lx-status a2" :status="session('status')" />

                    <div class="lx-field a3 lx-field-1">
                        <label class="lx-label" for="email">Email address</label>
                        <div class="lx-input-wrap margin-left-2">
                            <span class="lx-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </span>
                            <x-text-input id="email" type="email" name="email" placeholder="you@example.com" :value="old('email')" autofocus autocomplete="username" class="lx-input" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="lx-blade-error" />
                    </div>

                    <div class="lx-field a4 lx-field-2">
                        <label class="lx-label" for="password">Password</label>
                        <div class="lx-input-wrap">
                            <span class="lx-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <x-text-input id="password" type="password" name="password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="current-password" class="lx-input" />
                            <button type="button" id="togglePassword" class="lx-eye" aria-label="Toggle password">
                                <svg id="eyeOpen" class="hidden" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg id="eyeClosed" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="lx-blade-error" />
                    </div>

                    <div class="lx-meta a5">
                        <label class="lx-remember">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>{{ __('Remember me') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="lx-forgot">{{ __('Forgot password?') }}</a>
                        @endif
                    </div>

                    <div class="a6">
                        <button type="submit" id="login-btn" class="lx-btn">
                            {{ __('Sign in') }}
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </button>
                    </div>

                </form>

                @if (Route::has('register'))
                <div class="lx-div a7">or</div>
                <p class="lx-reg a7">
                    {{ __('Don\'t have an account?') }}
                    <a href="{{ route('register') }}">{{ __('Sign up for free') }}</a>
                </p>
                @endif

            </div>
        </div>
    </div>
</x-guest-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form");
        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");
        const togglePassword = document.getElementById("togglePassword");
        const eyeOpen = document.getElementById("eyeOpen");
        const eyeClosed = document.getElementById("eyeClosed");
        const loginBtn = document.getElementById("login-btn");

        function showError(input, message) {
            const outerWrapper = input.closest(".lx-field-1, .lx-field-2");
            if (!outerWrapper) return;
            outerWrapper.querySelectorAll(".js-error").forEach(e => e.remove());
            const bladeError = outerWrapper.querySelector(".lx-blade-error");
            if (bladeError && bladeError.textContent.trim()) {
                bladeError.style.display = "block";
            } else {
                const p = document.createElement("p");
                p.className = "js-error";
                p.innerText = message;
                outerWrapper.appendChild(p);
            }
            const wrap = input.closest(".lx-input-wrap");
            if (wrap) wrap.classList.add("has-error");
        }

        function clearErrors(input) {
            const outerWrapper = input.closest(".lx-field-1, .lx-field-2");
            if (outerWrapper) {
                outerWrapper.querySelectorAll(".js-error").forEach(e => e.remove());
                const bladeError = outerWrapper.querySelector(".lx-blade-error");
                if (bladeError) bladeError.style.display = "none";
            }
            const wrap = input.closest(".lx-input-wrap");
            if (wrap) wrap.classList.remove("has-error");
        }

        emailInput.addEventListener("input", function() {
            const val = this.value.trim();
            if (!val) {
                showError(this, "Email required");
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                showError(this, "Invalid email");
            } else {
                clearErrors(this);
            }
        });

        passwordInput.addEventListener("input", function() {
            if (!this.value.trim()) {
                showError(this, "Password required");
            } else {
                clearErrors(this);
            }
        });

        form.addEventListener("submit", function(e) {
            let valid = true;
            document.querySelectorAll(".js-error").forEach(el => el.remove());
            document.querySelectorAll(".lx-input-wrap").forEach(w => w.classList.remove("has-error"));

            if (!emailInput.value.trim()) {
                showError(emailInput, "Email required");
                valid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                showError(emailInput, "Invalid email");
                valid = false;
            }
            if (!passwordInput.value.trim()) {
                showError(passwordInput, "Password required");
                valid = false;
            }
            if (!valid) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:8px"><span style="display:inline-block;width:14px;height:14px;border:2px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.7s linear infinite"></span>Signing in...</span>';
        }, true);

        togglePassword.addEventListener("click", function() {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            eyeOpen.classList.toggle("hidden", !isPassword);
            eyeClosed.classList.toggle("hidden", isPassword);
        });

        const style = document.createElement("style");
        style.textContent = "@keyframes spin { to { transform: rotate(360deg); } }";
        document.head.appendChild(style);
    });
</script>