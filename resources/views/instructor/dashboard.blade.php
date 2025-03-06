<h1>Instructor Dashboard</h1>
<form method="POST" action="{{ route('instructor.logout') }}">
  @csrf
  <button type="submit">Logout</button>
</form>