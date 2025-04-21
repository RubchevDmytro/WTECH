<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
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
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <img src="{{ asset('images/box.png') }}" alt="Default Product Image">
                @endif
                <h3>{{ $product->name }}</h3>
                <p>{{ str_repeat('⭐', $product->rating) . str_repeat('☆', 5 - $product->rating) }}</p>
                <p>{{ $product->price }} €</p>
                <p>Stock: {{ $product->left_stock ?? 0 }}</p>
                <p>Category: {{ $product->subcategory ? ($product->subcategory->category ? $product->subcategory->category->name : 'N/A') : 'N/A' }}</p>
                <p>Subcategory: {{ $product->subcategory ? $product->subcategory->name : 'N/A' }}</p>
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

