@extends('layouts.app')
@section('title', 'Profile Settings')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

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

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
            dark:border-gray-700 shadow-sm p-6 mb-6">

        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">
            Profile photo
        </h2>

        <div class="flex items-center gap-6">

            <div class="relative flex-shrink-0">
                <div id="avatar-display"
                    class="w-24 h-24 rounded-full overflow-hidden
                        bg-blue-700 flex items-center justify-center
                        text-white text-3xl font-bold ring-4
                        ring-gray-100 dark:ring-gray-700">
                    @if(auth()->user()->avatar)
                    <img id="avatar-img"
                        src="{{ asset('storage/' . auth()->user()->avatar) }}"
                        alt="{{ auth()->user()->name }}"
                        class="w-full h-full object-cover">
                    @else
                    <span id="avatar-initials">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </span>
                    @endif
                </div>

                <label for="avatar-file-input"
                    class="absolute bottom-0 right-0 w-8 h-8 bg-blue-700
                          hover:bg-blue-600 rounded-full flex items-center
                          justify-center cursor-pointer shadow-lg
                          border-2 border-white dark:border-gray-800
                          transition">
                    <svg class="w-4 h-4 text-white" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2
                             2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2
                             0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2
                             0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <input type="file"
                        id="avatar-file-input"
                        accept="image/*"
                        class="hidden"
                        onchange="openAvatarCropper(this)">
                </label>
            </div>

            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-1">
                    {{ auth()->user()->name }}
                </h3>
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">
                    JPG, PNG, WEBP or GIF. Max size 5MB.<br>
                    Image will be cropped to a square.
                </p>
                <div class="flex gap-2">
                    <label for="avatar-file-input"
                        class="cursor-pointer inline-flex items-center gap-1.5
                              bg-blue-700 hover:bg-blue-800 text-white text-xs
                              font-medium px-3 py-2 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0
                                 0L8 8m4-4v12" />
                        </svg>
                        Upload photo
                    </label>

                    @if(auth()->user()->avatar)
                    <button type="button"
                        onclick="removeAvatar()"
                        class="inline-flex items-center gap-1.5 text-xs
                                   font-medium text-red-500 dark:text-red-400
                                   hover:text-red-700 dark:hover:text-red-300
                                   border border-red-200 dark:border-red-800
                                   hover:border-red-400 px-3 py-2 rounded-lg
                                   transition"
                        id="remove-avatar-btn">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Remove photo
                    </button>
                    @else
                    <button type="button"
                        onclick="removeAvatar()"
                        class="hidden inline-flex items-center gap-1.5 text-xs
                                   font-medium text-red-500 dark:text-red-400
                                   hover:text-red-700 border border-red-200
                                   dark:border-red-800 px-3 py-2 rounded-lg transition"
                        id="remove-avatar-btn">
                        Remove photo
                    </button>
                    @endif
                </div>

                {{-- Upload status --}}
                <p id="avatar-status" class="text-xs mt-2 hidden"></p>
            </div>
        </div>
    </div>

    <div id="cropper-modal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
        style="background-color: rgba(0,0,0,0.75);">

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl
                w-full max-w-lg p-6">

            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white text-base">
                    Crop your photo
                </h3>
                <button onclick="closeCropper()"
                    class="text-gray-400 hover:text-gray-600
                           dark:hover:text-gray-200 text-2xl font-bold">
                    &times;
                </button>
            </div>

            <div class="relative bg-gray-900 rounded-xl overflow-hidden mb-4"
                style="height: 320px;">
                <img id="cropper-image"
                    src=""
                    alt="Crop preview"
                    class="max-w-full">
            </div>

            <div class="flex items-center gap-3 mb-5">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0
                         0v3m0-3h3m-3 0H7" />
                </svg>
                <input type="range"
                    id="zoom-slider"
                    min="0"
                    max="3"
                    step="0.1"
                    value="0"
                    class="flex-1 accent-blue-600">
                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="flex gap-3">
                <button onclick="cropAndUpload()"
                    id="crop-save-btn"
                    class="flex-1 bg-blue-700 hover:bg-blue-800 text-white
                           font-medium py-2.5 rounded-xl text-sm transition
                           flex items-center justify-center gap-2">
                    <svg id="crop-spinner"
                        class="hidden w-4 h-4 animate-spin"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2
                             5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824
                             3 7.938l3-2.647z" />
                    </svg>
                    <span id="crop-btn-text">Save photo</span>
                </button>
                <button onclick="closeCropper()"
                    class="px-5 py-2.5 text-sm text-gray-500 dark:text-gray-400
                           hover:bg-gray-100 dark:hover:bg-gray-700
                           rounded-xl transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

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

        <form id="send-verification"
            method="POST"
            action="{{ route('verification.send') }}"
            class="hidden">
            @csrf
        </form>
    </div>

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

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100
                dark:border-gray-700 shadow-sm p-6 mb-6">

        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">
            Your activity
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-blue-700 dark:text-blue-400 mb-1">
                    {{ auth()->user()->boards()->count() }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Boards
                </div>
            </div>

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

            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">
                    {{ auth()->user()->comments()->count() }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                    Comments
                </div>
            </div>

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

@section('scripts')
{{-- Cropper.js from CDN --}}
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js">
</script>

<script>
    let cropperInstance = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function openAvatarCropper(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];

        if (file.size > 5 * 1024 * 1024) {
            showAvatarStatus('File too large. Maximum 5MB.', 'error');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('cropper-image');
            img.src = e.target.result;

            document.getElementById('cropper-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }

            document.getElementById('zoom-slider').value = 0;

            cropperInstance = new Cropper(img, {
                aspectRatio: 1, 
                viewMode: 2,
                dragMode: 'move',
                autoCropArea: 0.85,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: false,
                cropBoxResizable: false,
                toggleDragModeOnDblclick: false,
            });
        };
        reader.readAsDataURL(file);
        input.value = ''; 
    }

    document.getElementById('zoom-slider').addEventListener('input', function() {
        if (cropperInstance) {
            cropperInstance.zoomTo(parseFloat(this.value));
        }
    });

    function closeCropper() {
        document.getElementById('cropper-modal').classList.add('hidden');
        document.body.style.overflow = '';
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
    }

    async function cropAndUpload() {
        if (!cropperInstance) return;

        const btn = document.getElementById('crop-save-btn');
        const spinner = document.getElementById('crop-spinner');
        const btnText = document.getElementById('crop-btn-text');

        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Saving...';

        const canvas = cropperInstance.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        const base64 = canvas.toDataURL('image/jpeg', 0.92);

        try {
            const res = await fetch('/profile/avatar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    avatar: base64
                }),
            });

            const data = await res.json();

            if (!res.ok) {
                showAvatarStatus(data.message || 'Upload failed.', 'error');
                return;
            }

            if (data.success) {
                updateAvatarDisplay(data.avatar_url);

                updateNavbarAvatar(data.avatar_url);

                closeCropper();
                showAvatarStatus('Profile photo updated!', 'success');

                document.getElementById('remove-avatar-btn')
                    .classList.remove('hidden');
            }
        } catch (err) {
            showAvatarStatus('Upload failed. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            spinner.classList.add('hidden');
            btnText.textContent = 'Save photo';
        }
    }

    async function removeAvatar() {
        const confirmed = await showWarningModal({
            title: 'Remove Profile Photo',
            message: 'Remove your profile photo?',
            confirmText: 'Remove Photo'
        });

        if (!confirmed) return;

        try {
            const res = await fetch('/profile/avatar', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            const data = await res.json();

            if (data.success) {
                const display = document.getElementById('avatar-display');
                const initials = '{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}';

                display.innerHTML = `
                <span id="avatar-initials"
                      class="text-white text-3xl font-bold">
                    ${initials}
                </span>`;

                resetNavbarAvatar(initials);

                document.getElementById('remove-avatar-btn')
                    .classList.add('hidden');

                showAvatarStatus('Profile photo removed.', 'success');
            }
        } catch {
            showAvatarStatus('Failed to remove photo.', 'error');
        }
    }

    function updateAvatarDisplay(url) {
        const display = document.getElementById('avatar-display');
        display.innerHTML = `
        <img id="avatar-img"
             src="${url}"
             alt="Avatar"
             class="w-full h-full object-cover">`;
    }

    function updateNavbarAvatar(url) {
        // Find the navbar avatar — could be img or span
        const navAvatar = document.querySelector('nav img[alt="{{ auth()->user()->name }}"]');
        if (navAvatar) {
            navAvatar.src = url;
        } else {
            const navSpan = document.querySelector('nav .rounded-full');
            if (navSpan) {
                const img = document.createElement('img');
                img.src = url;
                img.alt = '{{ auth()->user()->name }}';
                img.className = 'w-8 h-8 rounded-full ring-2 ring-white/30 object-cover';
                navSpan.replaceWith(img);
            }
        }
    }

    function resetNavbarAvatar(initials) {
        const navImg = document.querySelector('nav img.rounded-full');
        if (navImg) {
            const span = document.createElement('span');
            span.className = 'w-8 h-8 rounded-full bg-white/25 flex items-center ' +
                'justify-center font-bold text-xs ring-2 ring-white/30 ' +
                'text-white';
            span.textContent = initials;
            navImg.replaceWith(span);
        }
    }

    function showAvatarStatus(message, type) {
        const el = document.getElementById('avatar-status');
        el.textContent = message;
        el.className = 'text-xs mt-2 ' +
            (type === 'error' ? 'text-red-500' : 'text-green-600 dark:text-green-400');
        el.classList.remove('hidden');
        setTimeout(() => el.classList.add('hidden'), 4000);
    }

    document.getElementById('cropper-modal').addEventListener('click', function(e) {
        if (e.target === this) closeCropper();
    });

    // Warning Modal Helper Function
    async function showWarningModal(options) {
        const modal = document.getElementById('warning-modal');
        if (!modal) {
            return window.confirm(options.message || 'Are you sure you want to proceed?');
        }

        const modalData = modal.__x;
        if (!modalData || !modalData.$data || typeof modalData.$data.show !== 'function') {
            return window.confirm(options.message || 'Are you sure you want to proceed?');
        }

        return await modalData.$data.show(options);
    }
</script>
@endsection