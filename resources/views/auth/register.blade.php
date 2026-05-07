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
        0%, 100% { background-position: 50% 50%; }
        50% { background-position: 52% 48%; }
    }

    @keyframes title-in {
        from { opacity: 0; letter-spacing: 0.35em; }
        to { opacity: 1; letter-spacing: 0.12em; }
    }

    @keyframes fade-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes line-grow {
        from { transform: scaleX(0); }
        to { transform: scaleX(1); }
    }

    .lx-shell { animation: bg-breathe 12s ease-in-out infinite; background-size: 200% 200%; }
    .lx-title { animation: title-in 1.1s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-f1 { animation: fade-up 0.7s 0.20s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-f2 { animation: fade-up 0.7s 0.30s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-f3 { animation: fade-up 0.7s 0.40s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-f4 { animation: fade-up 0.7s 0.50s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-l1 { animation: line-grow 0.7s 0.25s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-l2 { animation: line-grow 0.7s 0.35s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-l3 { animation: line-grow 0.7s 0.45s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-l4 { animation: line-grow 0.7s 0.55s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-btn-wrap { animation: fade-up 0.7s 0.62s cubic-bezier(0.22, 1, 0.36, 1) both; }
    .lx-register { animation: fade-up 0.7s 0.72s cubic-bezier(0.22, 1, 0.36, 1) both; }

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

    .lx-input::placeholder { color: #888 !important; }
    .lx-input:focus { color: #f5f5f5 !important; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation: none !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>

<x-guest-layout>
    <div class="lx-shell fixed inset-0 overflow-y-auto"
        style="background: radial-gradient(ellipse at 50% 60%, #2e2e2e 0%, #1a1a1a 38%, #0d0d0d 72%, #080808 100%); font-family: 'Cormorant Garamond', 'Georgia', 'Times New Roman', serif;">

        <div class="absolute inset-0 pointer-events-none"
            style="background: radial-gradient(ellipse at 50% 100%, rgba(55,48,30,0.18) 0%, transparent 55%);"></div>

        <div class="relative z-10 min-h-screen w-full flex items-center justify-center py-12">
            <div class="w-full max-w-[480px] px-10 text-center sm:px-6">
                <h1 class="lx-title text-white font-light mb-10 tracking-[0.12em]"
                    style="font-size: clamp(1.7rem, 5vw, 2.4rem);">
                    Create Account
                </h1>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="lx-f1 mb-9">
                        <div class="form-field flex items-end gap-4">
                            <div class="flex-shrink-0 w-5 h-5 mb-2.5 text-neutral-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="lx-l1 flex-1 relative border-b border-neutral-600 focus-within:border-neutral-400 transition-colors duration-300">
                                <x-text-input id="name" type="text" name="name" placeholder="Full Name"
                                    :value="old('name')" autofocus autocomplete="name" class="lx-input" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('name')"
                            class="lx-blade-error text-[0.7rem] font-sans text-red-400 mt-1.5 text-left tracking-[0.03em] block ml-9" />
                    </div>

                    {{-- Email --}}
                    <div class="lx-f2 mb-9">
                        <div class="form-field flex items-end gap-4">
                            <div class="flex-shrink-0 w-5 h-5 mb-2.5 text-neutral-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="lx-l2 flex-1 relative border-b border-neutral-600 focus-within:border-neutral-400 transition-colors duration-300">
                                <x-text-input id="email" type="email" name="email" placeholder="Email ID"
                                    :value="old('email')" autocomplete="username" class="lx-input" />
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')"
                            class="lx-blade-error text-[0.7rem] font-sans text-red-400 mt-1.5 text-left tracking-[0.03em] block ml-9" />
                    </div>

                    {{-- Password --}}
                    <div class="lx-f3 mb-2">
                        <div class="form-field flex items-end gap-4">
                            <div class="flex-shrink-0 w-5 h-5 mb-2.5 text-neutral-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="lx-l3 flex-1 relative border-b border-neutral-600 focus-within:border-neutral-400 transition-colors duration-300">
                                <x-text-input id="password" type="password" name="password" placeholder="Password"
                                    autocomplete="new-password" class="lx-input" />
                                <button type="button" data-toggle="password"
                                    class="absolute right-0 bottom-2 bg-transparent border-none cursor-pointer text-neutral-500 hover:text-neutral-300 transition-colors duration-200 flex items-center p-0"
                                    aria-label="Toggle password">
                                    <svg id="eyeOpenPassword" class="hidden w-[15px] h-[15px]" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeClosedPassword" class="w-[15px] h-[15px]" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.956 9.956 0 012.223-3.592M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.543 7a9.956 9.956 0 01-4.293 5.774M15 12a3 3 0 00-3-3m0 0a3 3 0 00-2.121.879M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')"
                            class="lx-blade-error text-[0.7rem] font-sans text-red-400 mt-1.5 text-left tracking-[0.03em] block ml-9" />
                    </div>

                    {{-- Password helper --}}
                    <div id="password-helper" class="mb-6 ml-9 text-left">
                        <ul class="space-y-0.5">
                            <li id="req-length" class="text-[0.68rem] font-sans tracking-wide text-neutral-500">• At least 8 characters</li>
                            <li id="req-upper" class="text-[0.68rem] font-sans tracking-wide text-neutral-500">• One uppercase letter</li>
                            <li id="req-number" class="text-[0.68rem] font-sans tracking-wide text-neutral-500">• One number</li>
                            <li id="req-symbol" class="text-[0.68rem] font-sans tracking-wide text-neutral-500">• One special character</li>
                        </ul>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="lx-f4 mb-9">
                        <div class="form-field flex items-end gap-4">
                            <div class="flex-shrink-0 w-5 h-5 mb-2.5 text-neutral-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div class="lx-l4 flex-1 relative border-b border-neutral-600 focus-within:border-neutral-400 transition-colors duration-300">
                                <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                                    placeholder="Confirm Password" autocomplete="new-password" class="lx-input" />
                                <button type="button" data-toggle="password"
                                    class="absolute right-0 bottom-2 bg-transparent border-none cursor-pointer text-neutral-500 hover:text-neutral-300 transition-colors duration-200 flex items-center p-0"
                                    aria-label="Toggle confirm password">
                                    <svg id="eyeOpenConfirm" class="hidden w-[15px] h-[15px]" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeClosedConfirm" class="w-[15px] h-[15px]" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.956 9.956 0 012.223-3.592M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.543 7a9.956 9.956 0 01-4.293 5.774M15 12a3 3 0 00-3-3m0 0a3 3 0 00-2.121.879M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')"
                            class="lx-blade-error text-[0.7rem] font-sans text-red-400 mt-1.5 text-left tracking-[0.03em] block ml-9" />
                    </div>

                    <div class="lx-btn-wrap">
                        <button type="submit"
                            class="block w-full py-4 px-4 bg-transparent border border-neutral-500 text-neutral-200 font-sans text-[0.72rem] font-semibold tracking-[0.28em] uppercase cursor-pointer transition-all duration-300 hover:border-neutral-300 hover:text-white hover:bg-white/[0.04] active:scale-[0.99] rounded-none">
                            {{ __('Register') }}
                        </button>
                    </div>
                </form>

                <p class="lx-register mt-8 font-sans text-[0.72rem] text-neutral-400 tracking-[0.04em]">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}"
                        class="text-neutral-300 hover:text-white border-b border-neutral-500 hover:border-neutral-300 pb-px transition-colors duration-200 no-underline">
                        {{ __('Sign in') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", function() {
                const wrapper = this.closest(".form-field")?.parentElement;
                if (wrapper) wrapper.querySelectorAll(".js-error").forEach(e => e.remove());

                // Hide Laravel blade errors
                const outerWrapper = this.closest(".lx-f1, .lx-f2, .lx-f3, .lx-f4");
                if (outerWrapper) {
                    outerWrapper.querySelectorAll(".lx-blade-error").forEach(e => {
                        e.style.display = "none";
                    });
                }
            });
        });

        function showError(input, message) {
            const wrapper = input.closest(".form-field")?.parentElement;
            if (!wrapper) return;
            wrapper.querySelectorAll(".js-error").forEach(e => e.remove());
            const p = document.createElement("p");
            p.className = "text-red-400 text-[0.7rem] font-sans mt-1.5 text-left tracking-[0.03em] ml-9 js-error";
            p.innerText = message;
            wrapper.appendChild(p);
        }

        function clearError(input) {
            const wrapper = input.closest(".form-field")?.parentElement;
            if (wrapper) wrapper.querySelectorAll(".js-error").forEach(e => e.remove());
        }

        const name = document.getElementById("name");
        const email = document.getElementById("email");
        const password = document.getElementById("password");
        const confirm = document.getElementById("password_confirmation");

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
            el.style.color = valid ? "#22c55e" : "";
            el.classList.toggle("text-neutral-500", !valid);
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

        document.querySelector("form").addEventListener("submit", function(e) {
            let valid = true;

            if (!name.value.trim() || name.value.length < 3) {
                showError(name, "Valid name required");
                valid = false;
            }
            if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                showError(email, "Valid email required");
                valid = false;
            }

            const pwd = password.value;
            if (!pwd || !(pwd.length >= 8 && /[A-Z]/.test(pwd) && /[0-9]/.test(pwd) && /[^A-Za-z0-9]/.test(pwd))) {
                showError(password, "Weak password");
                valid = false;
            }
            if (!confirm.value || confirm.value !== pwd) {
                showError(confirm, "Passwords must match");
                valid = false;
            }

            if (!valid) e.preventDefault();
        });

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