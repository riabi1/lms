<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up as an instructor! Before getting started, please check your email for a verification link.') }}
    </div>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Once verified, you can log in to access your dashboard.') }}
        <a href="{{ route('instructor.login') }}" class="underline text-sm text-gray-600 hover:text-gray-900">
            {{ __('Click here to log in') }}
        </a>.
    </div>

    @if (session('status'))
        <div class="mt-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif
</x-guest-layout>