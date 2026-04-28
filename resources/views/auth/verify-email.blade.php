<style>
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

    .lx-shell {
        animation: bg-breathe 12s ease-in-out infinite;
        background-size: 200% 200%;
    }

    .lx-title {
        animation: title-in 1.1s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-desc {
        animation: fade-up 0.7s 0.18s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-status {
        animation: fade-up 0.7s 0.28s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-btn-wrap {
        animation: fade-up 0.7s 0.38s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .lx-logout {
        animation: fade-up 0.7s 0.48s cubic-bezier(0.22, 1, 0.36, 1) both;
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

            <div class="lx-title flex justify-center mb-6">
                <div class="w-14 h-14 rounded-full border border-neutral-700 flex items-center justify-center text-neutral-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.2" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <h1 class="lx-title text-white font-light mb-4 tracking-[0.12em]"
                style="font-size: clamp(1.7rem, 5vw, 2.4rem);">
                Verify Email
            </h1>

            <p class="lx-desc font-sans text-[0.78rem] text-neutral-400 tracking-[0.03em] leading-relaxed mb-6">
                {{ __("Thanks for signing up! Please verify your email address by clicking the link we just sent you. If you didn't receive it, we'll send another.") }}
            </p>

            @if (session('status') == 'verification-link-sent')
            <div class="lx-status mb-6 font-sans text-[0.75rem] text-green-400 tracking-[0.03em] border border-green-900 bg-green-950/40 py-3 px-4 rounded-sm">
                {{ __('A new verification link has been sent to your email address.') }}
            </div>
            @endif

            <div class="lx-btn-wrap mb-5">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full py-4 px-4 bg-transparent border border-neutral-500 text-neutral-200 font-sans text-[0.72rem] font-semibold tracking-[0.28em] uppercase cursor-pointer transition-all duration-300 hover:border-neutral-300 hover:text-white hover:bg-white/[0.04] active:scale-[0.99] rounded-none">
                        {{ __('Resend Verification Email') }}
                    </button>
                </form>
            </div>

            <div class="lx-logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="font-sans text-[0.72rem] text-neutral-400 tracking-[0.04em] border-b border-neutral-600 hover:text-white hover:border-neutral-300 pb-px transition-colors duration-200 bg-transparent cursor-pointer italic">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-guest-layout>