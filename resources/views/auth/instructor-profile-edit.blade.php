<!DOCTYPE html>
<html>

<head>
  <title>Edit Instructor Profile</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <h1>Edit Instructor Profile</h1>

  @if (session('status'))
  <p>{{ session('status') }}</p>
  @endif

  <!-- Update Name -->
  <form method="POST" action="{{ route('instructor.profile.update') }}">
    @csrf
    @method('PATCH')
    <div>
      <label for="name">Name:</label>
      <input type="text" name="name" id="name" value="{{ old('name', $instructor->name) }}" required>
      @error('name')
      <span>{{ $message }}</span>
      @enderror
    </div>
    <button type="submit">Update Name</button>
  </form>

  <!-- Update Password -->
  <form method="POST" action="{{ route('instructor.profile.updatePassword') }}">
    @csrf
    @method('PUT')
    <div>
      <label for="current_password">Current Password:</label>
      <input type="password" name="current_password" id="current_password" required>
      @error('current_password')
      <span>{{ $message }}</span>
      @enderror
    </div>
    <div>
      <label for="password">New Password:</label>
      <input type="password" name="password" id="password" required>
      @error('password')
      <span>{{ $message }}</span>
      @enderror
    </div>
    <div>
      <label for="password_confirmation">Confirm Password:</label>
      <input type="password" name="password_confirmation" id="password_confirmation" required>
    </div>
    <button type="submit">Update Password</button>
  </form>

</html>