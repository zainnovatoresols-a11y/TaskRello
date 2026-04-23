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
</style>
<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
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
                        class="h-5 w-5  text-white"
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

        <!-- Confirm Password -->
        <div class="mt-4">
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
                        class="h-5 w-5  text-white"
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
        document.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", function() {

                // go to the wrapper div of this field
                const wrapper = this.closest("div");

                // target ONLY Laravel Breeze error message
                const error = wrapper.querySelector(".text-red-600");

                if (error) {
                    error.remove(); // remove instantly
                }
            });
        });
    });


    document.querySelectorAll("input").forEach(input => {
        input.addEventListener("input", function() {
            const error = this.closest("div").querySelector(".text-red-600");
            if (error) error.remove();
        });
    });

    document.querySelectorAll("[data-toggle='password']").forEach(button => {
        button.addEventListener("click", function() {
            const wrapper = this.closest("div");
            const input = wrapper.querySelector("input");

            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";

            const eyeOpen = wrapper.querySelector("svg[id^='eyeOpen']");
            const eyeClosed = wrapper.querySelector("svg[id^='eyeClosed']");

            eyeOpen.classList.toggle("hidden", !isPassword);
            eyeClosed.classList.toggle("hidden", isPassword);
        });
    });
</script>