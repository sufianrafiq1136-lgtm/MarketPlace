<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information.") }}
        </p>
    </header>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST"
          action="{{ route('profile.update') }}"
          enctype="multipart/form-data"
          class="mt-6 space-y-6">

        @csrf
        @method('PATCH')

        <!-- Profile Image -->
        <div>
            <x-input-label for="profile_image" :value="__('Profile Image')" />

            @if($user->profile_image)
                <img
                    src="{{ asset('storage/' . $user->profile_image) }}"
                    alt="Profile Image"
                    class="w-24 h-24 rounded-full object-cover mb-4 border"
                >
            @else
                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=128"
                    alt="Avatar"
                    class="w-24 h-24 rounded-full object-cover mb-4 border"
                >
            @endif

            <input
                id="profile_image"
                name="profile_image"
                type="file"
                class="block w-full mt-2 text-sm
                file:mr-4
                file:py-2
                file:px-4
                file:rounded-md
                file:border-0
                file:bg-indigo-600
                file:text-white
                hover:file:bg-indigo-700"
            >

            <x-input-error class="mt-2" :messages="$errors->get('profile_image')" />
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
            />

            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="__('Phone')" />

            <x-text-input
                id="phone"
                name="phone"
                type="text"
                class="mt-1 block w-full"
                :value="old('phone', $user->phone)"
                required
            />

            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- City -->
        <div>
            <x-input-label for="city" :value="__('City')" />

            <x-text-input
                id="city"
                name="city"
                type="text"
                class="mt-1 block w-full"
                :value="old('city', $user->city)"
            />

            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
            />

            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button
                            form="send-verification"
                            class="underline text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-600">
                            {{ __('A new verification link has been sent.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                {{ __('Save Changes') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600"
                >
                    Saved Successfully.
                </p>
            @endif
        </div>

    </form>
</section>