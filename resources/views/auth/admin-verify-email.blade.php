<x-guest-layout>
  <div class="mb-4 text-sm text-gray-600">
    {{ __('Thanks for signing up as an Admin! Before you can proceed, please check your email for a verification link.') }}
  </div>

  @if (session('status') == 'verification-link-sent')
  <div class="mb-4 font-medium text-sm text-green-600">
    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
  </div>
  @endif

  <div class="mt-4 flex items-center justify-between">
    <form method="POST" action="{{ route('admin.verification.send') }}">
      @csrf
      <div>
        <x-primary-button>
          {{ __('Resend Verification Email') }}
        </x-primary-button>
      </div>
    </form>

    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
        {{ __('Log Out') }}
      </button>
    </form>
  </div>
</x-guest-layout>