<form method="POST" action="{{ route(auth()->guard('admin')->check() ? 'admin.profile.update' : (auth()->guard('instructor')->check() ? 'instructor.profile.update' : 'profile.update')) }}">
    @csrf
    @method('PATCH')

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div class="mt-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div class="mt-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>
    </div>

    @if (session('status') === 'profile-updated')
        <p class="mt-2 text-sm text-green-600">{{ __('Profile updated successfully.') }}</p>
    @endif
</form>