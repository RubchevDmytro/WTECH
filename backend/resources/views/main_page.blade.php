<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
        .success {
            color: green;
            font-size: 18px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    <h1>Welcome to the Main Page!</h1>
    <p>You are logged in as {{ Auth::user()->email }}</p>
    <a href="{{ route('login.form') }}">Log out</a>
</body>
</html>
