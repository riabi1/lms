<form method="POST" action="{{ route(auth()->guard('admin')->check() ? 'admin.profile.update.password' : (auth()->guard('instructor')->check() ? 'instructor.profile.update.password' : 'profile.update.password')) }}">
    @csrf
    @method('PATCH')

    <div>
        <x-input-label for="current_password" :value="__('Current Password')" />
        <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" required />
        <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
    </div>

    <div class="mt-4">
        <x-input-label for="password" :value="__('New Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div class="mt-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
    </div>

    <div class="mt-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>
    </div>

    @if (session('status') === 'password-updated')
        <p class="mt-2 text-sm text-green-600">{{ __('Password updated successfully.') }}</p>
    @endif
</form>