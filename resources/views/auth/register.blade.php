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

    .lx-left-inner { position: relative; z-index: 2; padding-top: 24px; }

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
        border: 2px solid var(--bg-card);
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
    flex: 1; display: flex; flex-direction: column;
    justify-content: flex-start; align-items: center;
    padding: 40px 48px; position: relative; z-index: 2; overflow-y: auto;
}

.lx-form-wrap {
    width: 100%;
    max-width: 420px;
    margin: auto 0;
    padding: 20px 0;
}

    .lx-form-wrap {
        width: 100%;
        max-width: 420px;
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
    font-size: 0.855rem; color: var(--text-secondary);
    margin-bottom: 28px; line-height: 1.6;
}

    /* Fields */
    .lx-field { 
        margin-bottom: 14px; 
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

    /* Password strength */
    .lx-strength {
        margin-top: 10px;
        margin-bottom: 4px;
    }

    .lx-strength-bars {
        display: flex;
        gap: 4px;
        margin-bottom: 6px;
    }

    .lx-strength-bar {
        flex: 1;
        height: 3px;
        border-radius: 2px;
        background: var(--border-hover);
        transition: background 0.3s;
    }

    .lx-strength-bar.active-weak {
        background: #f87171;
    }

    .lx-strength-bar.active-fair {
        background: #fbbf24;
    }

    .lx-strength-bar.active-strong {
        background: #34d399;
    }

    .lx-reqs {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .lx-req {
        font-size: 0.7rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s;
    }

    .lx-req.met {
        color: var(--success);
    }

    .lx-req-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: currentColor;
        flex-shrink: 0;
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

    /* Button */
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
        margin-top: 8px;
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
        margin: 24px 0 16px;
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

    .a8 {
        animation: lx-up 0.5s 0.48s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation: none !important;
            transition-duration: 0.01ms !important;
            opacity: 1 !important;
        }
    }

    @media (max-width: 959px) {
        .lx-right {
            padding: 48px 28px;
        }

        .lx-input {
            padding: 13px 10px 13px 0 !important;
            font-size: 1rem !important;
        }

        .lx-btn {
            padding: 15px 24px;
        }
    }
</style>

<x-guest-layout>
    <div class="lx-shell">
        <div class="lx-orb lx-orb-1"></div>
        <div class="lx-orb lx-orb-2"></div>
        <div class="lx-orb lx-orb-3"></div>
        <div class="lx-grid"></div>

        {{-- Left panel — identical to login --}}
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
                    <h2>Join the<br>team, <em>today.</em></h2>
                    <p>Create your account and start building something great with your team.</p>
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

        {{-- Right panel — register form --}}
        <div class="lx-right">
            <div class="lx-form-wrap">

                <div class="a1">
                    <h1 class="lx-title">Create account</h1>
                    <p class="lx-subtitle">Fill in your details to get started for free.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" data-skip-loader>
                    @csrf

                    {{-- Name --}}
                    <div class="lx-field a2 lx-f1">
                        <label class="lx-label" for="name">Full name</label>
                        <div class="lx-input-wrap">
                            <span class="lx-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                            <x-text-input id="name" type="text" name="name" placeholder="Your full name"
    :value="old('name')" autofocus autocomplete="name" spellcheck="false"
    autocorrect="off" autocapitalize="words" class="lx-input"/>
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="lx-blade-error" />
                    </div>

                    {{-- Email --}}
                    <div class="lx-field a3 lx-f2">
                        <label class="lx-label" for="email">Email address</label>
                        <div class="lx-input-wrap">
                            <span class="lx-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </span>
                            <x-text-input id="email" type="email" name="email" placeholder="you@example.com"
                                :value="old('email')" autocomplete="username" class="lx-input" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="lx-blade-error" />
                    </div>

                    {{-- Password --}}
                    <div class="lx-field a4 lx-f3">
                        <label class="lx-label" for="password">Password</label>
                        <div class="lx-input-wrap">
                            <span class="lx-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <x-text-input id="password" type="password" name="password"
                                placeholder="Min. 8 characters" autocomplete="new-password" class="lx-input" />
                            <button type="button" data-toggle="password" class="lx-eye" aria-label="Toggle password">
                                <svg id="eyeOpenPassword" class="hidden" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg id="eyeClosedPassword" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="lx-blade-error" />

                        {{-- Strength indicator --}}
                        <div class="lx-strength" id="password-helper" style="display:none;">
                            <div class="lx-strength-bars">
                                <div class="lx-strength-bar" id="bar1"></div>
                                <div class="lx-strength-bar" id="bar2"></div>
                                <div class="lx-strength-bar" id="bar3"></div>
                                <div class="lx-strength-bar" id="bar4"></div>
                            </div>
                            <div class="lx-reqs">
                                <div class="lx-req" id="req-length">
                                    <div class="lx-req-dot"></div><span>At least 8 characters</span>
                                </div>
                                <div class="lx-req" id="req-upper">
                                    <div class="lx-req-dot"></div><span>One uppercase letter</span>
                                </div>
                                <div class="lx-req" id="req-number">
                                    <div class="lx-req-dot"></div><span>One number</span>
                                </div>
                                <div class="lx-req" id="req-symbol">
                                    <div class="lx-req-dot"></div><span>One special character</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="lx-field a5 lx-f4">
                        <label class="lx-label" for="password_confirmation">Confirm password</label>
                        <div class="lx-input-wrap">
                            <span class="lx-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </span>
                            <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="Repeat your password" autocomplete="new-password" class="lx-input" />
                            <button type="button" data-toggle="password" class="lx-eye" aria-label="Toggle confirm password">
                                <svg id="eyeOpenConfirm" class="hidden" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg id="eyeClosedConfirm" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="lx-blade-error" />
                    </div>

                    <div class="a6">
                        <button type="submit" id="register-btn" class="lx-btn">
                            <span id="btn-text">{{ __('Create account') }}</span>
                            <svg id="btn-spinner" class="hidden" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </button>
                    </div>

                </form>

                <div class="lx-div a7">or</div>
                <p class="lx-reg a7">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}">{{ __('Sign in') }}</a>
                </p>

            </div>
        </div>
    </div>
</x-guest-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.querySelectorAll("input").forEach(input => {
    input.addEventListener("input", function() {
        const field = this.closest(".lx-field");
        if (field) {
            field.querySelectorAll(".js-error").forEach(e => e.remove());
            field.querySelectorAll(".lx-blade-error").forEach(e => e.style.display = "none");
            const wrap = this.closest(".lx-input-wrap");
            if (wrap) wrap.classList.remove("has-error");
        }
    });
});

        function showError(input, message) {
    const field = input.closest(".lx-field");
    if (!field) return;
    field.querySelectorAll(".js-error").forEach(e => e.remove());
    const wrap = input.closest(".lx-input-wrap");
    if (wrap) wrap.classList.add("has-error");
    const p = document.createElement("p");
    p.className = "js-error";
    p.style.cssText = "font-size:0.75rem;color:#f87171;margin-top:6px;padding-left:4px;font-family:'DM Sans',sans-serif;";
    p.innerText = message;
    field.appendChild(p);
}

function clearError(input) {
    const field = input.closest(".lx-field");
    if (!field) return;
    field.querySelectorAll(".js-error").forEach(e => e.remove());
    const wrap = input.closest(".lx-input-wrap");
    if (wrap) wrap.classList.remove("has-error");
}

        const name = document.getElementById("name");
        const email = document.getElementById("email");
        const password = document.getElementById("password");
        const confirm = document.getElementById("password_confirmation");

        // Immediately attach submit listener to prevent any submission
        const form = document.querySelector("form");
        const registerBtn = document.getElementById("register-btn");
        const btnText = document.getElementById("btn-text");
        const btnSpinner = document.getElementById("btn-spinner");

        form.addEventListener("submit", function(e) {
            let valid = true;
            let errorCount = 0;

            if (!name.value.trim() || name.value.length < 3) {
                showError(name, "Valid name required");
                valid = false;
                errorCount++;
            }
            if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                showError(email, "Valid email required");
                valid = false;
                errorCount++;
            }

            const pwd = password.value;
            if (!pwd || !(pwd.length >= 8 && /[A-Z]/.test(pwd) && /[0-9]/.test(pwd) && /[^A-Za-z0-9]/.test(pwd))) {
                showError(password, "Weak password");
                valid = false;
                errorCount++;
            }
            if (!confirm.value || confirm.value !== pwd) {
                showError(confirm, "Passwords must match");
                valid = false;
                errorCount++;
            }

            // If any errors, prevent submission
            if (!valid) {
                e.preventDefault();
                e.stopPropagation();
                console.warn(`Form validation failed with ${errorCount} error(s)`);
                return false;
            }

            // If valid, show loading spinner and allow form submission
            registerBtn.disabled = true;
            btnText.textContent = "Registering...";
            btnSpinner.classList.remove("hidden");
        }, true); // Use capture phase to catch before other handlers

        name.addEventListener("input", function() {
            const val = this.value.trim();
            if (!val) return clearError(this);
            if (val.length < 3) showError(this, "Name must be at least 3 characters");
            else if (!/^[a-zA-Z\s]+$/.test(val)) showError(this, "Only letters allowed");
            else clearError(this);
        });

        email.addEventListener("input", function() {
            const val = this.value.trim();
            if (!val) return clearError(this);
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) showError(this, "Invalid email");
            else clearError(this);
        });

        function setReq(id, valid) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle("met", valid);
        }

        password.addEventListener("input", function() {
            const val = this.value;
            const hasLen = val.length >= 8;
            const hasUpper = /[A-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);
            const hasSymbol = /[^A-Za-z0-9]/.test(val);

            setReq("req-length", hasLen);
            setReq("req-upper", hasUpper);
            setReq("req-number", hasNumber);
            setReq("req-symbol", hasSymbol);
            // show helper on first keystroke
            const helper = document.getElementById("password-helper");
            if (helper) helper.style.display = val ? "block" : "none";

            // update strength bars
            const score = [hasLen, hasUpper, hasNumber, hasSymbol].filter(Boolean).length;
            const barClass = score <= 1 ? "active-weak" : score <= 3 ? "active-fair" : "active-strong";
            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById("bar" + i);
                if (bar) {
                    bar.className = "lx-strength-bar";
                    if (i <= score) bar.classList.add(barClass);
                }
            }

            if (val && val.length < 8) showError(this, "Password must be at least 8 characters");
            else clearError(this);

            if (confirm.value) validateConfirm();
        });

        function validateConfirm() {
            if (!confirm.value) return clearError(confirm);
            if (confirm.value !== password.value) showError(confirm, "Passwords do not match");
            else clearError(confirm);
        }

        confirm.addEventListener("input", validateConfirm);

        document.querySelectorAll("[data-toggle='password']").forEach(button => {
            button.addEventListener("click", function() {
                const wrapper = this.closest(".lx-l3, .lx-l4") ?? this.parentElement;
                const input = wrapper?.querySelector("input");
                if (!input) return;

                const isPassword = input.type === "password";
                input.type = isPassword ? "text" : "password";

                const eyeOpen = wrapper.querySelector("svg[id^='eyeOpen']");
                const eyeClosed = wrapper.querySelector("svg[id^='eyeClosed']");
                if (eyeOpen && eyeClosed) {
                    eyeOpen.classList.toggle("hidden", !isPassword);
                    eyeClosed.classList.toggle("hidden", isPassword);
                }
            });
        });
    });
</script>