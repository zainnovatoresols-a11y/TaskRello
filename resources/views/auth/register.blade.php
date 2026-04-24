<style>
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 1000px #1f2937 inset !important;
        box-shadow: 0 0 0 1000px #1f2937 inset !important;
        -webkit-text-fill-color: #fff !important;
        caret-color: #fff;
        transition: background-color 9999s ease-in-out 0s;
    }

    /* === ONLY CHANGE: Consistent field wrapper for alignment === */
    .form-field {
        position: relative;
    }
</style>

<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-field">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="form-field mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="form-field mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10"
                    type="password"
                    name="password"
                    autocomplete="new-password" />

                <button type="button" data-toggle="password"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white">

                    <!-- Eye Open -->
                    <svg id="eyeOpenPassword"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 hidden text-gray-500 dark:text-gray-300"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 
        4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <!-- Eye Closed -->
                    <svg id="eyeClosedPassword"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.956 
        9.956 0 012.223-3.592M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 
        8.268 2.943 9.543 7a9.956 9.956 0 01-4.293 5.774M15 12a3 3 0 
        00-3-3m0 0a3 3 0 00-2.121.879M3 3l18 18" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div id="password-helper" class="mt-2 text-xs text-gray-400 space-y-1">
            <ul class="space-y-1">
                <li id="req-length">• At least 8 characters</li>
                <li id="req-upper">• One uppercase letter</li>
                <li id="req-number">• One number</li>
                <li id="req-symbol">• One special character</li>
            </ul>
        </div>

        <!-- Confirm Password -->
        <div class="form-field mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password" />

                <button type="button" data-toggle="password"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white">

                    <!-- Eye Open -->
                    <svg id="eyeOpenConfirm"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 hidden text-gray-500 dark:text-gray-300"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 
        4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <!-- Eye Closed -->
                    <svg id="eyeClosedConfirm"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.956 
        9.956 0 012.223-3.592M6.223 6.223A9.956 9.956 0 0112 5c4.478 0 
        8.268 2.943 9.543 7a9.956 9.956 0 01-4.293 5.774M15 12a3 3 0 
        00-3-3m0 0a3 3 0 00-2.121.879M3 3l18 18" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Clear errors on input - target the correct wrapper
        document.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", function() {
                const wrapper = this.closest(".form-field");
                if (wrapper) {
                    wrapper.querySelectorAll(".js-error").forEach(e => e.remove());
                }
            });
        });

        function showError(input, message) {
            const wrapper = input.closest(".form-field");
            if (!wrapper) return;

            // Remove existing custom errors only
            wrapper.querySelectorAll(".js-error").forEach(e => e.remove());

            const p = document.createElement("p");
            p.className = "text-red-600 text-sm mt-2 js-error";
            p.innerText = message;

            // Insert error AFTER the input/x-input-error, before next field
            const laravelError = wrapper.querySelector("x-input-error");
            if (laravelError && laravelError.nextSibling) {
                wrapper.insertBefore(p, laravelError.nextSibling);
            } else if (laravelError) {
                laravelError.after(p);
            } else {
                wrapper.appendChild(p);
            }
        }

        function clearError(input) {
            const wrapper = input.closest(".form-field");
            if (wrapper) {
                wrapper.querySelectorAll(".js-error").forEach(e => e.remove());
            }
        }

        const name = document.getElementById("name");
        const email = document.getElementById("email");
        const password = document.getElementById("password");
        const confirm = document.getElementById("password_confirmation");

        name.addEventListener("input", function() {
            const val = this.value.trim();

            if (!val) return clearError(this);

            if (val.length < 3) {
                showError(this, "Name must be at least 3 characters");
            } else if (!/^[a-zA-Z\s]+$/.test(val)) {
                showError(this, "Only letters allowed");
            } else {
                clearError(this);
            }
        });

        email.addEventListener("input", function() {
            const val = this.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!val) return clearError(this);

            if (!regex.test(val)) {
                showError(this, "Invalid email");
            } else {
                clearError(this);
            }
        });

        const strengthText = document.getElementById("pwd-strength-text");

        function setReq(id, valid) {
            const el = document.getElementById(id);
            if (!el) return;

            el.style.color = valid ? "#22c55e" : "#9ca3af";
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

            // Only update strength text if element exists
            if (strengthText) {
                const score = [hasLen, hasUpper, hasNumber, hasSymbol].filter(Boolean).length;
                const labels = ["", "Weak", "Fair", "Good", "Strong"];
                const colors = ["", "#ef4444", "#f97316", "#eab308", "#22c55e"];

                if (!val) {
                    strengthText.innerText = "Enter password";
                    strengthText.style.color = "#9ca3af";
                } else {
                    strengthText.innerText = labels[score];
                    strengthText.style.color = colors[score];
                }
            }

            // basic validation
            if (val && val.length < 8) {
                showError(this, "Password must be at least 8 characters");
            } else {
                clearError(this);
            }

            if (confirm.value) validateConfirm();
        });

        function validateConfirm() {
            if (!confirm.value) return clearError(confirm);

            if (confirm.value !== password.value) {
                showError(confirm, "Passwords do not match");
            } else {
                clearError(confirm);
            }
        }

        confirm.addEventListener("input", validateConfirm);

        document.querySelector("form").addEventListener("submit", function(e) {

            let valid = true;

            if (!name.value.trim() || name.value.length < 3) {
                showError(name, "Valid name required");
                valid = false;
            }

            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim() || !regex.test(email.value)) {
                showError(email, "Valid email required");
                valid = false;
            }

            const pwd = password.value;
            const hasLen = pwd.length >= 8;
            const hasUpper = /[A-Z]/.test(pwd);
            const hasNumber = /[0-9]/.test(pwd);
            const hasSymbol = /[^A-Za-z0-9]/.test(pwd);

            if (!pwd || !(hasLen && hasUpper && hasNumber && hasSymbol)) {
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
                const wrapper = this.closest(".relative");
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