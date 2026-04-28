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

    .lx-desc {
        animation: fade-up 0.7s 0.15s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-f1 {
        animation: fade-up 0.7s 0.28s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-l1 {
        animation: line-grow 0.7s 0.33s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-btn-wrap {
        animation: fade-up 0.7s 0.48s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-back {
        animation: fade-up 0.7s 0.58s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-input {
        width: 100%;
        background: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0.45rem 0.5rem 0.6rem 0 !important;
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

            <h1 class="lx-title text-white font-light mb-4 tracking-[0.12em]"
                style="font-size: clamp(1.7rem, 5vw, 2.4rem);">
                Reset Password
            </h1>

            <p class="lx-desc font-sans text-[0.78rem] text-neutral-400 tracking-[0.03em] leading-relaxed mb-10">
                {{ __("Forgot your password? No problem. Enter your email and we'll send you a reset link.") }}
            </p>

            <x-auth-session-status
                class="mb-6 text-xs font-sans text-green-300 tracking-[0.04em]"
                :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="lx-f1 flex items-end gap-4 mb-9">
                    <div class="flex-shrink-0 w-5 h-5 mb-2.5 text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="lx-l1 flex-1 relative border-b border-neutral-600 focus-within:border-neutral-400 transition-colors duration-300">
                        <x-text-input
                            id="email"
                            type="email"
                            name="email"
                            placeholder="Email ID"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="email"
                            class="lx-input" />
                        <x-input-error
                            :messages="$errors->get('email')"
                            class="text-[0.7rem] font-sans text-red-400 mt-1.5 text-left tracking-[0.03em] block" />
                    </div>
                </div>

                <div class="lx-btn-wrap">
                    <button type="submit"
                        class="block w-full py-4 px-4 bg-transparent border border-neutral-500 text-neutral-200 font-sans text-[0.72rem] font-semibold tracking-[0.28em] uppercase cursor-pointer transition-all duration-300 hover:border-neutral-300 hover:text-white hover:bg-white/[0.04] active:scale-[0.99] rounded-none">
                        {{ __('Send Reset Link') }}
                    </button>
                </div>
            </form>

            <p class="lx-back mt-8 font-sans text-[0.72rem] text-neutral-400 tracking-[0.04em]">
                {{ __('Remembered your password?') }}
                <a href="{{ route('login') }}"
                    class="text-neutral-300 hover:text-white border-b border-neutral-500 hover:border-neutral-300 pb-px transition-colors duration-200 no-underline">
                    {{ __('Sign in') }}
                </a>
            </p>

        </div>
    </div>
</x-guest-layout>
