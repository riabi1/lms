<h1>Admin Dashboard</h1>
<form method="POST" action="{{ route('admin.logout') }}">
  @csrf
  <button type="submit">Logout</button>
</form>