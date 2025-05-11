<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }
        .container {
            width: 80%;
            margin: 80px auto;
            background: #ddd;
            padding: 20px;
            border-radius: 10px;
        }
        .product {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .product_image {
            width: 80px;
            height: 80px;
        }
        .total {
            font-size: 18px;
            margin-top: 20px;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        button {
            background: #800000;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #a00000;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
        .success {
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }
        /* Стили для выпадающего списка категорий */
        .category-dropdown {
            position: relative;
            display: inline-block;
        }
        .category-btn {
            background-color: #f0f0f0;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .category-btn:hover {
            background-color: #e0e0e0;
        }
        .category-menu {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            top: 100%;
            left: 0;
            min-width: 150px;
        }
        .category-menu a {
            display: block;
            padding: 8px 12px;
            color: #333;
            text-decoration: none;
        }
        .category-menu a:hover {
            background-color: #f0f0f0;
        }
        .category-menu.show {
            display: block;
        }
    </style>
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
                        <input type="text" placeholder="Hľadať..." name="search" id="search-input" value="{{ request()->query('search') }}" autocomplete="off">
                        <div id="autocomplete-suggestions" class="autocomplete-suggestions"></div>
                    </div>
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>
            <!-- Добавляем выпадающий список категорий -->
            <div class="category-dropdown">
                <button class="category-btn">Kategórie</button>
                <div class="category-menu">
                    @if(isset($categories) && $categories->isNotEmpty())
                        @foreach($categories as $category)
                            <a href="{{ route('main_page', ['category' => $category->id]) }}">{{ $category->name }}</a>
                        @endforeach
                    @else
                        <a href="{{ route('main_page') }}">Žiadne kategórie</a>
                    @endif
                </div>
            </div>
            <div class="nav-right">
                <!-- Показываем корзину всем пользователям -->
                <a href="{{ route('cart') }}" class="cart-link {{ Request::is('cart') ? 'active' : '' }}">🛒 Košík</a>
                @auth
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.menu') }}" class="nav-item {{ Request::is('admin*') ? 'active' : '' }}">Admin Panel</a>
                    @endif
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        @method('POST')
                        <button type="submit" class="nav-item logout-btn">Odhlásiť sa</button>
                    </form>
                @else
                    <a href="{{ route('login.form') }}" class="nav-item {{ Request::is('login*') ? 'active' : '' }}">Prihlásiť sa</a>
                @endif
            </div>
        </nav>
    </div>
</header>

<main class="container">
    <section id="cart">
        @forelse ($cartItems as $item)
            <article class="product" data-price="{{ $item->product->price }}">
                @if ($item->product->primary_image)
                    <img class="product_image" src="data:{{ $item->product->primary_image->mime_type }};base64,{{ $item->product->primary_image->image_data }}" alt="{{ $item->product->name }}">
                @endif
                <span class="name">{{ $item->product->name }}</span>
                <div class="controls">
                    <form action="{{ route('cart.update', $item->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" name="action" value="decrease" class="minus">-</button>
                        <span class="count">{{ $item->quantity }}</span>
                        <button type="submit" name="action" value="increase" class="plus">+</button>
                    </form>
                </div>
                <span class="price">${{ $item->product->price * $item->quantity }}</span>
                <form action="{{ route('cart.remove', $item->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete">🗑</button>
                </form>
            </article>
        @empty
            <p>Your cart is empty.</p>
        @endforelse
    </section>

    @if ($cartItems->isNotEmpty())
        <div class="total">Total: $<span id="total-price">{{ $totalPrice }}</span></div>
        <div class="buttons">
            <a href="{{ route('order.confirm') }}"><button type="button" id="confirm-order">Confirm Order</button></a>
        </div>
    @endif

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif
</main>

<footer>
    <p>© 2025 Store | Sledujte nás na sociálnych sieťach | O nás</p>
</footer>

<script src="{{ asset('js/main_page.js') }}"></script>
<script>
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.product').forEach(product => {
            let count = parseInt(product.querySelector('.count').textContent);
            let price = parseFloat(product.dataset.price);
            total += count * price;
        });
        document.getElementById('total-price').textContent = total.toFixed(2);
    }

    updateTotal();

    // JavaScript для управления выпадающим списком категорий
    document.addEventListener("DOMContentLoaded", function () {
        const categoryBtn = document.querySelector(".category-btn");
        const categoryMenu = document.querySelector(".category-menu");

        categoryBtn.addEventListener("click", function () {
            categoryMenu.classList.toggle("show");
        });

        // Закрытие меню при клике вне его
        document.addEventListener("click", function (e) {
            if (!categoryBtn.contains(e.target) && !categoryMenu.contains(e.target)) {
                categoryMenu.classList.remove("show");
            }
        });
    });
</script>
</body>
</html>
