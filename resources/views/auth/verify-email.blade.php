<!DOCTYPE html>
<html>

<head>
  <title>Verify Email</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <h1>Please Verify Your Email</h1>
  <p>Check your email for a verification link.</p>

  @if (session('message'))
  <p>{{ session('message') }}</p>
  @endif

  <form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit">Resend Verification Email</button>
  </form>

  <script>
    // Check verification status every 2 seconds
    setInterval(() => {
      fetch('/check-verification', {
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.verified) {
            window.location.reload(); // Reload the page
            // The controller will redirect to dashboard if verified
          }
        })
        .catch(error => console.log('Error checking verification:', error));
    }, 2000);
  </script>
</body>

</html>