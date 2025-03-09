<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up as an instructor! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('message'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('instructor.verification.send') }}">
        @csrf

        <div class="mt-4 flex items-center justify-between">
            <x-primary-button>
                {{ __('Resend Verification Email') }}
            </x-primary-button>

            <a href="{{ route('instructor.logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </a>

            <form id="logout-form" action="{{ route('instructor.logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function checkVerification() {
                fetch('{{ route('verification.check') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.verified) {
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }

            setInterval(checkVerification, 5000); // Poll every 5 seconds
        });
    </script>
</x-guest-layout>