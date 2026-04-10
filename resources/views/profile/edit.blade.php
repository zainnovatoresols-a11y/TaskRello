@extends('layouts.app')
@section('title', 'Profile Settings')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Page header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('boards.index') }}"
           class="text-gray-400 dark:text-gray-500 hover:text-gray-600
                  dark:hover:text-gray-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Profile settings
            </h1>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">
                Manage your account information and security
            </p>
        </div>
    </div>

    {{-- ── Avatar section ───────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 shadow-sm p-6 mb-6">

        <div class="flex items-center gap-5">

            {{-- Avatar circle with initials --}}
            <div class="w-20 h-20 rounded-full bg-blue-700 flex items-center
                        justify-center text-white text-2xl font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ auth()->user()->name }}
                </h2>
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    {{ auth()->user()->email }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    Member since {{ auth()->user()->created_at->format('F Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ── Update profile information ───────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 shadow-sm p-6 mb-6">

        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-1">
            Personal information
        </h2>
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-5">
            Update your name and email address
        </p>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            {{-- Name --}}
            <div class="mb-5">
                <label for="name"
                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Full name
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', auth()->user()->name) }}"
                       required
                       autofocus
                       autocomplete="name"
                       class="w-full border rounded-lg px-3 py-2.5 text-sm
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-gray-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              focus:border-transparent transition
                              {{ $errors->has('name')
                                  ? 'border-red-400 dark:border-red-500'
                                  : 'border-gray-300 dark:border-gray-600' }}">
                @error('name')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-6">
                <label for="email"
                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Email address
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email', auth()->user()->email) }}"
                       required
                       autocomplete="username"
                       class="w-full border rounded-lg px-3 py-2.5 text-sm
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-gray-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              focus:border-transparent transition
                              {{ $errors->has('email')
                                  ? 'border-red-400 dark:border-red-500'
                                  : 'border-gray-300 dark:border-gray-600' }}">
                @error('email')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror

                {{-- Email verification notice --}}
                @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                    && ! auth()->user()->hasVerifiedEmail())
                    <div class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border
                                border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <p class="text-xs text-yellow-700 dark:text-yellow-400">
                            Your email address is unverified.
                            <button form="send-verification"
                                    class="underline hover:no-underline font-medium">
                                Click here to re-send the verification email.
                            </button>
                        </p>
                        @if(session('status') === 'verification-link-sent')
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-medium">
                                A new verification link has been sent to your email.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2.5
                               rounded-lg text-sm font-medium transition shadow-sm">
                    Save changes
                </button>
                @if(session('status') === 'profile-updated')
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                        Profile updated successfully.
                    </span>
                @endif
            </div>
        </form>

        {{-- Hidden form for resend verification --}}
        <form id="send-verification"
              method="POST"
              action="{{ route('verification.send') }}"
              class="hidden">
            @csrf
        </form>
    </div>

    {{-- ── Change password ───────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 shadow-sm p-6 mb-6">

        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-1">
            Change password
        </h2>
        <p class="text-xs text-gray-400 dark:text-gray-500 mb-5">
            Use a strong password of at least 8 characters
        </p>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')

            {{-- Current password --}}
            <div class="mb-5">
                <label for="current_password"
                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Current password
                </label>
                <input type="password"
                       id="current_password"
                       name="current_password"
                       autocomplete="current-password"
                       class="w-full border rounded-lg px-3 py-2.5 text-sm
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-gray-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              focus:border-transparent transition
                              {{ $errors->updatePassword->has('current_password')
                                  ? 'border-red-400 dark:border-red-500'
                                  : 'border-gray-300 dark:border-gray-600' }}">
                @if($errors->updatePassword->has('current_password'))
                    <p class="text-red-500 text-xs mt-1.5">
                        {{ $errors->updatePassword->first('current_password') }}
                    </p>
                @endif
            </div>

            {{-- New password --}}
            <div class="mb-5">
                <label for="password"
                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    New password
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       autocomplete="new-password"
                       class="w-full border rounded-lg px-3 py-2.5 text-sm
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-gray-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              focus:border-transparent transition
                              {{ $errors->updatePassword->has('password')
                                  ? 'border-red-400 dark:border-red-500'
                                  : 'border-gray-300 dark:border-gray-600' }}">
                @if($errors->updatePassword->has('password'))
                    <p class="text-red-500 text-xs mt-1.5">
                        {{ $errors->updatePassword->first('password') }}
                    </p>
                @endif
            </div>

            {{-- Confirm new password --}}
            <div class="mb-6">
                <label for="password_confirmation"
                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    Confirm new password
                </label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       autocomplete="new-password"
                       class="w-full border border-gray-300 dark:border-gray-600
                              rounded-lg px-3 py-2.5 text-sm
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-gray-100
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              focus:border-transparent transition">
                @if($errors->updatePassword->has('password_confirmation'))
                    <p class="text-red-500 text-xs mt-1.5">
                        {{ $errors->updatePassword->first('password_confirmation') }}
                    </p>
                @endif
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-blue-700 hover:bg-blue-800 text-white px-5 py-2.5
                               rounded-lg text-sm font-medium transition shadow-sm">
                    Update password
                </button>
                @if(session('status') === 'password-updated')
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                        Password updated successfully.
                    </span>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Account stats ─────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 shadow-sm p-6 mb-6">

        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">
            Your activity
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

            {{-- Boards --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-400 mb-1">
                    {{ auth()->user()->boards()->count() }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Boards
                </div>
            </div>

            {{-- Cards created --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                    {{ auth()->user()->ownedBoards()
                        ->withCount('lists')
                        ->get()
                        ->sum(fn($b) => \App\Models\Card::whereIn(
                            'list_id',
                            \App\Models\BoardList::where('board_id', $b->id)
                                ->pluck('id')
                        )->where('user_id', auth()->id())->count()) }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Cards created
                </div>
            </div>

            {{-- Comments --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">
                    {{ auth()->user()->comments()->count() }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Comments
                </div>
            </div>

            {{-- Assigned cards --}}
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-orange-500 dark:text-orange-400 mb-1">
                    {{ auth()->user()->assignedCards()->count() }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Assigned cards
                </div>
            </div>

        </div>
    </div>

    {{-- ── Danger zone — delete account ─────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-100
                dark:border-red-900/50 shadow-sm p-6">

        <h2 class="text-base font-semibold text-red-600 dark:text-red-400 mb-1">
            Danger zone
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Once you delete your account all of your data will be permanently
            removed. This action cannot be undone.
        </p>

        <button onclick="document.getElementById('delete-account-modal').classList.remove('hidden')"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5
                       rounded-lg text-sm font-medium transition">
            Delete my account
        </button>
    </div>
</div>

{{-- ── Delete account confirmation modal ───────────────────── --}}
<div id="delete-account-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background-color: rgba(0,0,0,0.55);">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6">

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30
                        flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                             1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                             0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                Delete account
            </h3>
        </div>

        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 leading-relaxed">
            Are you sure you want to delete your account?
            All boards you own, along with their lists, cards,
            comments, and attachments will be permanently deleted.
            This action cannot be undone.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            {{-- Password confirmation --}}
            <div class="mb-5">
                <label for="delete_password"
                       class="block text-sm font-medium text-gray-700
                              dark:text-gray-300 mb-1.5">
                    Confirm your password
                </label>
                <input type="password"
                       id="delete_password"
                       name="password"
                       placeholder="Enter your password to confirm..."
                       class="w-full border border-gray-300 dark:border-gray-600
                              rounded-lg px-3 py-2.5 text-sm
                              bg-white dark:bg-gray-900
                              text-gray-900 dark:text-gray-100
                              placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-red-500
                              focus:border-transparent transition">
                @error('password', 'userDeletion')
                    <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5
                               rounded-lg text-sm font-medium transition">
                    Yes, delete my account
                </button>
                <button type="button"
                        onclick="document.getElementById('delete-account-modal')
                                         .classList.add('hidden')"
                        class="text-gray-500 dark:text-gray-400 px-5 py-2.5
                               rounded-lg text-sm hover:bg-gray-100
                               dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection