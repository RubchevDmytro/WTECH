<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
</head>
<body>
<header>
    <div class="navbar-wrapper">
        <nav class="navbar">
            <div class="logo">
                <a href="{{ route('main_page') }}" class="logo-link">🏠</a>
            </div>
            <div class="search-form">
                <form method="GET" action="{{ route('main_page') }}" class="search-form">
                    <div class="autocomplete-wrapper">
                        <input type="text" placeholder="Search..." name="search" value="{{ request()->query('search') }}" autocomplete="off">
                        <div id="autocomplete-suggestions" class="autocomplete-suggestions"></div>
                    </div>
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>
            <div class="nav-right">
                @auth
                    <span class="user-name">Logged in as: {{ Auth::user()->email }} (is_admin: {{ Auth::user()->is_admin ? 'true' : 'false' }})</span>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-item logout-btn">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login.form') }}" class="nav-item">Log In</a>
                @endauth
                <a href="{{ route('cart') }}" class="cart-link">🛒</a>
            </div>
        </nav>
    </div>
</header>

<main>
    <section class="products">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <h2 style="margin-top:60px;">Product Details</h2>
        <div class="product-list">
            <div class="product">
                @if ($product->primary_image)
                    <img src="data:{{ $product->primary_image->mime_type }};base64,{{ $product->primary_image->image_data }}" alt="{{ $product->name }}">
                @endif
                <h3>{{ $product->name }}</h3>
                <p>{{ str_repeat('⭐', $product->rating) . str_repeat('☆', 5 - $product->rating) }}</p>
                <p>{{ $product->price }} €</p>
                <p>Stock: {{ $product->stock ?? 0 }}</p>
                <p>Category: {{ $product->category ? $product->category->name : 'N/A' }}</p>
                @auth
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <input type="number" name="quantity" value="1" min="1" class="quantity">
                        <button type="submit">🛒 Add to Cart</button>
                    </form>
                @else
                    <p>Please <a href="{{ route('login.form') }}">log in</a> to add this product to your cart.</p>
                @endauth
                <a href="{{ route('main_page') }}">Back to Products</a>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>© 2025 Store | Follow us on social media | About us</p>
</footer>

<script src="{{ asset('js/main_page.js') }}"></script>
</body>
</html>
