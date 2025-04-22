<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
    <style>

        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
            background: #e8e1e1;
            border-radius: 30px;
        }

        .product-images {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .main-image img {
            width: 100%;
            max-width: 300px;
            height: auto;
        }

        .thumbnail-images {
            display: flex;
            gap: 10px;
        }

        .thumbnail-images img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            cursor: pointer;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .thumbnail-images img:hover {
            border-color: #a52a2a;
        }

        .product-info {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .product-info h3 {
            font-size: 24px;
            margin: 0;
        }

        .rating {
            font-size: 18px;
        }

        .product-info p {
            margin: 5px 0;
            color: #555;
        }

        .product-actions {
            flex: 1;
            background: #d5cbcb;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .product-actions .price {
            font-size: 20px;
            font-weight: bold;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-control button {
            width: 30px;
            height: 30px;
            background-color: #ddd;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .quantity-control input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }

        .add-to-cart-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .add-to-cart-btn:hover {
            background-color: #218838;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
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
    <section class="products">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        {{--        <h2 style="margin-top:60px; text-align: center;">Product Details</h2>--}}
        <div class="product-details">
            <!-- Изображения продукта -->
            <div class="product-images">
                <div class="main-image">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <img src="{{ asset('images/box.png') }}" alt="Default Product Image">
                    @endif
                </div>
                <div class="thumbnail-images">
                    <!-- Добавляем заглушки для дополнительных изображений -->
                    <img src="{{ asset('images/box.png') }}" alt="Thumbnail 1">
                    <img src="{{ asset('images/box.png') }}" alt="Thumbnail 2">
                    <img src="{{ asset('images/box.png') }}" alt="Thumbnail 3">
                    <img src="{{ asset('images/box.png') }}" alt="Thumbnail 4">
                </div>
            </div>

            <!-- Информация о продукте -->
            <div class="product-info">
                <h3>{{ $product->name }}</h3>
                <p class="rating">{{ str_repeat('⭐', $product->rating) . str_repeat('☆', 5 - $product->rating) }}</p>
                <p><strong>Description:</strong> {{ $product->description ?? 'No description available.' }}</p>
                <p><strong>Stock:</strong> {{ $product->stock ?? 0 }}</p>
                <p>
{{--                    <strong>Category:</strong> {{ $product->subcategory ? ($product->subcategory->category ? $product->subcategory->category->name : 'N/A') : 'N/A' }}--}}
                    <strong>Category:</strong> {{ $product->subcategory ? ($product->subcategory->category ? $product->subcategory->category->name : 'N/A') : 'N/A' }}
                </p>
                <p>
{{--                    <strong>Subcategory:</strong> {{ $product->subcategory ? $product->subcategory->name : 'N/A' }}--}}
                    <strong>Subcategory:</strong> {{ $product->category ? $product->category->name : 'N/A' }}
                </p>
            </div>
            <!-- Действия с продуктом -->
            <div class="product-actions">
                <div class="price">{{ $product->price }} €</div>
                @auth
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <div class="quantity-control">
                            <button type="button" onclick="this.nextElementSibling.stepDown()">-</button>
                            <input type="number" name="quantity" value="1" min="1" class="quantity">
                            <button type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                        </div>
                        <button type="submit" class="add-to-cart-btn">Add To Cart</button>
                    </form>
                @else
                    <p>Please <a href="{{ route('login.form') }}">log in</a> to add this product to your cart.</p>
                @endauth
            </div>
        </div>


        <a href="{{ route('main_page') }}" class="back-link">Back to Products</a>
    </section>
</main>

<footer>
    <p>© 2025 Store | Follow us on social media | About us</p>
</footer>

<script src="{{ asset('js/main_page.js') }}"></script>
</body>
</html>
