<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }
        header {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #a52a2a;
            width: 100%;
            position: fixed;
            z-index: 999;
        }
        .top-bar {
            display: flex;
            align-items: center;
            width: 100%;
        }
        .logo, .login, .cart-btn {
            font-size: 20px;
            margin: 0 10px;
            cursor: pointer;
            color: white;
            text-decoration: none;
        }
        input[type="text"] {
            flex: 1;
            padding: 5px;
            margin: 0 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
        .product_image {
            width: 80px;
            height: 80px;
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
    </style>
</head>
<body>
<header>
    <div class="top-bar">
        <a href="{{ route('main_page') }}" class="logo">🏠</a>
        <input type="text" placeholder="Search...">
        <button class="search-btn">🔍</button>
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
</script>
</body>
</html>
