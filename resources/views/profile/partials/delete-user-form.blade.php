<form method="POST" action="{{ route(auth()->guard('admin')->check() ? 'admin.profile.destroy' : (auth()->guard('instructor')->check() ? 'instructor.profile.destroy' : 'profile.destroy')) }}">
    @csrf
    @method('DELETE')

    <h3 class="text-lg font-medium text-gray-900">{{ __('Delete Account') }}</h3>
    <p class="mt-1 text-sm text-gray-600">{{ __('Are you sure you want to delete your account? This action cannot be undone.') }}</p>

    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div class="mt-4">
        <x-danger-button onclick="return confirm('Are you sure?')">{{ __('Delete Account') }}</x-danger-button>
    </div>
</form>