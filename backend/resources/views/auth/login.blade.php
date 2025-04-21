<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        header {
            height: 150px;
            align-items: center;
            width: 100%;
            margin-top: 40px;
        }

        .logo-image {
            width: 150px;
            height: 100%;
            margin-left: auto;
            margin-right: auto;
            display: block;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-size: cover;
        }

        .container {
            width: 80%;
            max-width: 400px;
            margin: 60px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .title {
            font-size: 24px;
            color: #8B0000;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 90%;
            padding: 10px;
            border: 1px solid #8B0000;
            border-radius: 20px;
            background: #f2f2f2;
            font-size: 16px;
            color: #333;
            text-align: center;
        }

        .submit-btn {
            display: block;
            width: 100%;
            padding: 12px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .submit-btn {
            background: #800000;
            color: white;
        }

        .submit-btn:hover {
            background: #a00000;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header>
    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-image"></header>

    <div class="container">
        <div class="title">Log In</div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="submit-btn">Submit</button>
        </form>

        <p>Have no account? <a href="{{ route('register.form') }}">Register</a></p>
    </div>
</body>
</html>
