<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product Image</title>
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
    <style>
        .upload-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .upload-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group select,
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group button {
            background-color: #a52a2a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .form-group button:hover {
            background-color: #8b1a1a;
        }

        .success, .error {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <div class="top-bar">
        <div class="menu-icon">☰</div>
        <a href="{{ route('main_page') }}" class="logo">🏠</a>
        <form method="GET" action="{{ route('main_page') }}" class="search-form">
            <input type="text" placeholder="Search..." name="search" value="{{ request()->query('search') }}">
            <button type="submit" class="search-btn">🔍</button>
        </form>
        @auth
            <div>
                Logged in as: {{ Auth::user()->email }} (is_admin: {{ Auth::user()->is_admin ? 'true' : 'false' }})
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="login">Log Out</button>
            </form>
        @else
            <a href="{{ route('login.form') }}" class="login">Log In</a>
        @endauth
        <a href="{{ route('cart') }}" class="cart-btn">🛒</a>
    </div>
</header>

<main>
    <div class="upload-container">
        <h2>Upload Product Image</h2>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('product.upload_image.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="product_id">Select Product</label>
                <select name="product_id" id="product_id" required>
                    <option value="">-- Select a product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="image">Upload Image</label>
                <input type="file" name="image" id="image" accept="image/*" required>
            </div>

            <div class="form-group">
                <button type="submit">Upload Image</button>
            </div>
        </form>

        <a href="{{ route('main_page') }}" class="back-link">Back to Products</a>
    </div>
</main>

<footer>
    <p>© 2025 Store | Follow us on social media | About us</p>
</footer>

<script src="{{ asset('js/main_page.js') }}"></script>
</body>
</html>
