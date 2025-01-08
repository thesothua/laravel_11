<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
</head>

<body>
    <form method="POST" action="/password/reset">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>
