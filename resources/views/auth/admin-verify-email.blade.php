<!DOCTYPE html>
<html>

<head>
  <title>Verify Admin Email</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <h1>Please Verify Your Admin Email</h1>
  <p>Check your email for a verification link.</p>

  @if (session('message'))
  <p>{{ session('message') }}</p>
  @endif

  <form method="POST" action="{{ route('admin.verification.send') }}">
    @csrf
    <button type="submit">Resend Verification Email</button>
  </form>

  <p id="status">Waiting for verification...</p>

  <script>
    setInterval(() => {
      fetch('/check-verification', {
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          console.log('Verification status:', data); // Debug output
          if (data.verified) {
            document.getElementById('status').textContent = 'Verified! Redirecting...';
            window.location.reload();
          }
        })
        .catch(error => console.error('Error checking verification:', error));
    }, 2000);
  </script>
</body>

</html>