<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obchod s hudobnými nástrojmi</title>
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
            <a href="{{ route('logout') }}" class="login" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{ Auth::user()->email }} (Log Out)
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @else
            <a href="{{ route('login.form') }}" class="login">Log In</a>
        @endauth
        <a href="{{ route('cart') }}" class="cart-btn">🛒</a>
    </div>
</header>

<main>
    <aside class="sidebar">
        <h2>Kategórie</h2>
        <ul class="category-list">
            @foreach($categories as $category)
                <li>
                    <strong>{{ $category->name }}</strong>
                    <ul>
                        @foreach($category->subcategories as $subcategory)
                            <li>
                                <a href="{{ route('main_page', ['category' => $subcategory->slug]) }}">
                                    {{ $subcategory->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </aside>

    <section class="products">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <div class="filter-bar">
            <form method="GET" action="{{ route('main_page') }}">
                <label>Filtrovať podľa:</label>
                <select name="sort">
                    <option value="price_asc" {{ request()->query('sort') == 'price_asc' ? 'selected' : '' }}>Rastúca cena</option>
                    <option value="price_desc" {{ request()->query('sort') == 'price_desc' ? 'selected' : '' }}>Klesajúca cena</option>
                    <option value="rating" {{ request()->query('sort') == 'rating' ? 'selected' : '' }}>Hodnotenie</option>
                </select>
                <label>Cena:</label>
                <div class="slider-container">
                    <div class="inputs">
                        <span>from</span>
                        <input type="number" id="min-price" name="min_price" value="{{ request()->query('min_price', 0) }}" min="0" max="1000">
                        <span>to</span>
                        <input type="number" id="max-price" name="max_price" value="{{ request()->query('max_price', 1000) }}" min="0" max="1000">
                    </div>
                    <div class="slider-wrapper">
                        <div class="slider" id="slider">
                            <div class="range-track"></div>
                            <div class="thumb" id="min-thumb"></div>
                            <div class="thumb" id="max-thumb"></div>
                        </div>
                    </div>
                </div>
                <button type="submit">OK</button>
            </form>
        </div>
        <h2 style="margin-top:60px;">Zoznam produktov</h2>
        <div class="product-list">
            @forelse($products as $product)
                <div class="product">
                    <a href="{{ route('product.show', $product->id) }}">
                        <img src="{{ asset('images/' . ($product->image ?? 'box.png')) }}" alt="{{ $product->name }}">
                    </a>
                    <h3>{{ $product->name }}</h3>
                    <p>{{ str_repeat('⭐', $product->rating) . str_repeat('☆', 5 - $product->rating) }}</p>
                    <p>{{ $product->price }} €</p>
                    <form method="POST" action="{{ route('cart.add', $product->id) }}">
                        @csrf
                        <input type="number" name="quantity" value="1" min="1" class="quantity">
                        <button type="submit">🛒 Add to cart</button>
                    </form>
                </div>
            @empty
                <p>Žiadne produkty nenájdené.</p>
            @endforelse
        </div>
        <div class="pagination" id="pagination">
            {{ $products->appends(request()->query())->links('vendor.pagination.default') }}
        </div>
    </section>
</main>
<footer>
    <p>© 2025 Store | Follow us on social media | About us</p>
</footer>

<script src="{{ asset('js/main_page.js') }}"></script>
</body>
</html>
