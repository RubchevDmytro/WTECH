<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="{{ asset('css/confirmation.css') }}">
</head>
<body>

<header>
    <a href="{{ route('main_page') }}">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-image">
    </a>
</header>

<main class="container">

    <section class="cart-review">
        <h2>Confirm Your Order</h2>
        @forelse ($cartItems as $item)
            <article class="product" data-price="{{ $item['price'] }}">
                <img class="product_image" src="{{ asset('images/' . ($item['image'] ?? 'box.png')) }}"
                     alt="{{ $item['name'] }}">
                <span class="name">{{ $item['name'] }}</span>
                <span class="count">{{ $item['quantity'] }}</span>
                <span class="price">${{ $item['price'] * $item['quantity'] }}</span>
            </article>
        @empty
            <p>Your cart is empty.</p>
        @endforelse
        <div class="total">Total: $<span id="total-price">{{ $totalPrice }}</span></div>
        <a href="{{ route('cart') }}">
            <button type="button">Back to Cart</button>
        </a>
    </section>

    @if (count($cartItems) > 0)
        <section class="confirmation-form">
            <form action="{{ route('order.create') }}" method="POST">
                @csrf
                {{--                    <label for="shipping_address">Shipping Address:</label>--}}
                {{--                    <input type="text" id="shipping_address" name="shipping_address" required>--}}

                <h2>Delivery Address</h2>
                <input type="text" placeholder="First Name">
                <input type="text" placeholder="Last Name">
                <input type="text" placeholder="Address" id="shipping_address" name="shipping_address" required>
                <input type="text" placeholder="City">
                <input type="text" placeholder="Postcode">
                <input type="text" placeholder="Country">


                <h2>Payment</h2>
                <input type="text" placeholder="Card Number">
                <input type="text" placeholder="Name on Card">
                <input type="text" placeholder="MM/YY">
                <input type="text" placeholder="CVV">

                <button type="submit" id="place-order" class="pay">Pay</button>

                @endif

                @if (session('error'))
                    <div class="error">{{ session('error') }}</div>
                @endif
            </form>
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
