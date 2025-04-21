<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
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
        .shipping-address {
            margin-top: 20px;
        }
        .shipping-address label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .shipping-address input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
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
    <section id="order-confirmation">
        <h2>Confirm Your Order</h2>
        @forelse ($cartItems as $item)
            <article class="product" data-price="{{ $item->product->price }}">
                <img class="product_image" src="{{ asset('images/' . ($item->product->image ?? 'box.png')) }}" alt="{{ $item->product->name }}">
                <span class="name">{{ $item->product->name }}</span>
                <span class="count">{{ $item->quantity }}</span>
                <span class="price">${{ $item->product->price * $item->quantity }}</span>
            </article>
        @empty
            <p>Your cart is empty.</p>
        @endforelse

        @if ($cartItems->isNotEmpty())
            <div class="total">Total: $<span id="total-price">{{ $totalPrice }}</span></div>
            <div class="shipping-address">
                <form action="{{ route('order.create') }}" method="POST">
                    @csrf
                    <label for="shipping_address">Shipping Address:</label>
                    <input type="text" id="shipping_address" name="shipping_address" required>
                    <div class="buttons">
                        <button type="submit" id="place-order">Place Order</button>
                        <a href="{{ route('cart') }}"><button type="button">Back to Cart</button></a>
                    </div>
                </form>
            </div>
        @endif

        @if (session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif
    </section>
</main>

<script>
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.product').forEach(product => {
            let count = parseInt(product.querySelector('.count').textContent);
            let price = parseInt(product.dataset.price);
            total += count * price;
        });
        document.getElementById('total-price').textContent = total;
    }

    updateTotal();
</script>
</body>
</html>
