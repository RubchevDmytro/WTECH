<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-size: cover;
        }
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
        .top-bar input {
            flex-grow: 1;
            margin: 0 10px;
            padding: 5px;
        }

        .container {
            width: 80%;
            max-width: 500px;
            margin: 40px auto;
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

        .form-group input, .form-group select {
            width: 90%;
            padding: 10px;
            border: 1px solid #8B0000;
            border-radius: 20px;
            background: #f2f2f2;
            font-size: 16px;
            color: #333;
            text-align: center;
        }

        .submit-btn, .login-btn {
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
            background: #8B0000;
            color: white;
        }

        .submit-btn:hover {
            background: #a52a2a;
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
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-image">
    </header>

    <div class="container">
        <div class="title">Registration</div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <input type="text" id="name" name="name" placeholder="Name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <input type="text" id="surname" name="surname" placeholder="Surname" value="{{ old('surname') }}" required>
                @error('surname')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
    <select id="gender" name="gender" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
    </select>
    @error('gender')
        <div class="error">{{ $message }}</div>
    @enderror
</div>
<div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <input type="password" id="confirm-password" name="password_confirmation" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="submit-btn">Create Account</button>
        </form>
    <p>Have an account? <a href="{{ route('login') }}">Log in</a></p></div>
</body>
</html>
