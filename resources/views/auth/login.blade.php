<style>
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px transparent inset !important;
        box-shadow: 0 0 0 1000px transparent inset !important;
        -webkit-text-fill-color: #e5e5e5 !important;
        caret-color: #e5e5e5;
        transition: background-color 9999s ease-in-out 0s;
    }

    @keyframes bg-breathe {

        0%,
        100% {
            background-position: 50% 50%;
        }

        50% {
            background-position: 52% 48%;
        }
    }

    @keyframes title-in {
        from {
            opacity: 0;
            letter-spacing: 0.35em;
        }

        to {
            opacity: 1;
            letter-spacing: 0.12em;
        }
    }

    @keyframes fade-up {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes line-grow {
        from {
            transform: scaleX(0);
        }

        to {
            transform: scaleX(1);
        }
    }

    .lx-shell {
        animation: bg-breathe 12s ease-in-out infinite;
        background-size: 200% 200%;
    }

    .lx-title {
        animation: title-in 1.1s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-field-1 {
        animation: fade-up 0.7s 0.25s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-field-2 {
        animation: fade-up 0.7s 0.38s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-line-1 {
        animation: line-grow 0.7s 0.35s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-line-2 {
        animation: line-grow 0.7s 0.48s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-meta {
        animation: fade-up 0.7s 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-btn-wrap {
        animation: fade-up 0.7s 0.62s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-register {
        animation: fade-up 0.7s 0.72s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-input {
        width: 100%;
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0.45rem 2rem 0.6rem 0 !important;
        font-size: 0.925rem !important;
        font-family: ui-sans-serif, system-ui, sans-serif !important;
        color: #e5e5e5 !important;
        letter-spacing: 0.05em;
        outline: none !important;
        box-shadow: none !important;
        caret-color: #bbb;
    }

    .lx-input::placeholder {
        color: #888 !important;
    }

    .lx-input:focus {
        color: #f5f5f5 !important;
    }

    .lx-remember input[type="checkbox"] {
        appearance: none;
        width: 13px;
        height: 13px;
        border: 1px solid #777;
        background: transparent;
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
        border-radius: 1px;
    }

    .lx-remember input[type="checkbox"]:checked::after {
        content: '';
        position: absolute;
        top: 1px;
        left: 3px;
        width: 5px;
        height: 8px;
        border: 1px solid #bbb;
        border-top: none;
        border-left: none;
        transform: rotate(43deg);
    }

    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {
            animation: none !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>

<x-guest-layout>
    <div class="lx-shell fixed inset-0 flex items-center justify-center overflow-hidden"
        style="background: radial-gradient(ellipse at 50% 60%, #2e2e2e 0%, #1a1a1a 38%, #0d0d0d 72%, #080808 100%); font-family: 'Cormorant Garamond', 'Georgia', 'Times New Roman', serif;">

        <div class="absolute inset-0 pointer-events-none"
            style="background: radial-gradient(ellipse at 50% 100%, rgba(55,48,30,0.18) 0%, transparent 55%);"></div>

        <div class="relative z-10 w-full max-w-[480px] px-10 text-center sm:px-6">

            <h1 class="lx-title text-white font-light mb-12 tracking-[0.12em]"
                style="font-size: clamp(1.7rem, 5vw, 2.4rem);">
                Login
            </h1>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <x-auth-session-status
                    class="mb-6 text-xs font-sans text-green-300 tracking-[0.04em]"
                    :status="session('status')" />

                <div class="lx-field-1 flex items-end gap-4 mb-9">
                    <div class="flex-shrink-0 w-5 h-5 mb-2.5 text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="lx-line-1 flex-1 relative border-b border-neutral-600 focus-within:border-neutral-400 transition-colors duration-300">
                        <x-text-input
                            id="email"
                            type="email"
                            name="email"
                            placeholder="Email ID"
                            :value="old('email')"
                            autofocus
                            autocomplete="username"
                            class="lx-input" />
                        <x-input-error
                            :messages="$errors->get('email')"
                            class="text-[0.7rem] font-sans text-red-400 mt-1.5 text-left tracking-[0.03em] block" />
                    </div>
                </div>

                <div class="lx-field-2 flex items-end gap-4 mb-9">
                    <div class="flex-shrink-0 w-5 h-5 mb-2.5 text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div class="lx-line-2 flex-1 relative border-b border-neutral-600 focus-within:border-neutral-400 transition-colors duration-300">
                        <x-text-input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Password"
                            autocomplete="current-password"
                            class="lx-input" />
                        <button type="button" id="togglePassword"
                            class="absolute right-0 bottom-2 bg-transparent border-none cursor-pointer text-neutral-500 hover:text-neutral-300 transition-colors duration-200 flex items-center p-0"
                            aria-label="Toggle password">
                            <svg id="eyeOpen" class="hidden w-[15px] h-[15px]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeClosed" class="w-[15px] h-[15px]" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.956 9.956 0 012.223-3.592M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.543 7a9.956 9.956 0 01-4.293 5.774M15 12a3 3 0 00-3-3m0 0a3 3 0 00-2.121.879M3 3l18 18" />
                            </svg>
                        </button>
                        <x-input-error
                            :messages="$errors->get('password')"
                            class="text-[0.7rem] font-sans text-red-400 mt-1.5 text-left tracking-[0.03em] block" />
                    </div>
                </div>

                <div class="lx-meta flex items-center justify-between mb-10 font-sans text-xs">
                    <label class="lx-remember flex items-center gap-2 text-neutral-300 cursor-pointer select-none">
                        <input id="remember_me" type="checkbox" name="remember">
                        <span>{{ __('Remember me') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="text-neutral-300 hover:text-white no-underline italic tracking-[0.02em] transition-colors duration-200 text-xs">
                        {{ __('Forgot Password?') }}
                    </a>
                    @endif
                </div>

                <div class="lx-btn-wrap">
                    <button type="submit"
                        class="block w-full py-4 px-4 bg-transparent border border-neutral-500 text-neutral-200 font-sans text-[0.72rem] font-semibold tracking-[0.28em] uppercase cursor-pointer transition-all duration-300 hover:border-neutral-300 hover:text-white hover:bg-white/[0.04] active:scale-[0.99] rounded-none">
                        {{ __('Login') }}
                    </button>
                </div>
            </form>

            @if (Route::has('register'))
            <p class="lx-register mt-8 font-sans text-[0.72rem] text-neutral-400 tracking-[0.04em]">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}"
                    class="text-neutral-300 hover:text-white border-b border-neutral-500 hover:border-neutral-300 pb-px transition-colors duration-200 no-underline">
                    {{ __('Sign up') }}
                </a>
            </p>
            @endif

        </div>
    </div>
</x-guest-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", function() {
                const err = this.closest(".lx-field-1, .lx-field-2")?.querySelector(".text-red-400");
                if (err) err.style.display = "none";
            });
        });

        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");
        const eyeOpen = document.getElementById("eyeOpen");
        const eyeClosed = document.getElementById("eyeClosed");

        togglePassword.addEventListener("click", function() {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            eyeOpen.classList.toggle("hidden", !isPassword);
            eyeClosed.classList.toggle("hidden", isPassword);
        });
    });
</script>
