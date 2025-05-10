<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="{{ asset('css/confirmation.css') }}">
</head>
<body>
    <header>
        <a href="{{ route('main_page') }}" class="logo-link">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-image">
        </a>
    </header>

    <main class="container">
        <section class="cart-review">
            <h2>Confirm Your Order</h2>

            @if ($cartItems->isEmpty())
                <p class="empty-cart">Your cart is empty.</p>
            @else
                <div class="product-list">
                    @foreach ($cartItems as $item)
                        <article class="product" data-price="{{ $item->product->price ?? 0 }}">
                            @if ($item->product && $item->product->primary_image)
                                <div class="product-image">
                                    <img src="data:{{ $item->product->primary_image->mime_type }};base64,{{ $item->product->primary_image->image_data }}" 
                                         alt="{{ $item->product->name ?? 'Product Image' }}" 
                                         class="product-image" 
                                         loading="lazy">
                                </div>
                            @else
                                <div class="product-image placeholder">No Image</div>
                            @endif
                            <div class="product-details">
                                <span class="name">{{ $item->product->name ?? 'Unknown Product' }}</span>
                                <span class="count">Qty: {{ $item->quantity }}</span>
                                <span class="price">Total: ${{ ($item->product->price ?? 0) * $item->quantity }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="total-section">
                    <p class="total">Total: $<span id="total-price">{{ number_format($totalPrice, 2) }}</span></p>
                    <a href="{{ route('cart') }}" class="back-button">
                        <button type="button" class="btn">Back to Cart</button>
                    </a>
                </div>
            @endif
        </section>

        @if (!$cartItems->isEmpty())
            <section class="confirmation-form">
                <form action="{{ route('order.create') }}" method="POST" id="order-form">
                    @csrf
                    <h2>Delivery Address</h2>
                    <div class="form-group">
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="text" name="shipping_address" placeholder="Address" required>
                        <input type="text" name="city" placeholder="City" required>
                        <input type="text" name="postcode" placeholder="Postcode" required>
                        <input type="text" name="country" placeholder="Country" required>
                    </div>

                    <h2>Payment</h2>
                    <div class="form-group">
                        <input type="text" name="card_number" placeholder="Card Number" required pattern="\d{16}">
                        <input type="text" name="card_name" placeholder="Name on Card" required>
                        <input type="text" name="expiry_date" placeholder="MM/YY" required pattern="(0[1-9]|1[0-2])/[0-9]{2}">
                        <input type="text" name="cvv" placeholder="CVV" required pattern="\d{3,4}">
                    </div>

                    <button type="submit" id="place-order" class="btn pay">Place Order</button>

                    @if (session('error'))
                        <div class="error-message">{{ session('error') }}</div>
                    @endif
                </form>
            </section>
        @endif
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Update total price dynamically (if needed in the future)
            function updateTotal() {
                let total = 0;
                document.querySelectorAll('.product').forEach(product => {
                    let count = parseInt(product.querySelector('.count').textContent.replace('Qty: ', ''));
                    let price = parseFloat(product.dataset.price);
                    total += count * price;
                });
                document.getElementById('total-price').textContent = total.toFixed(2);
            }

            // Initial call (though total is already calculated server-side)
            updateTotal();

            // Form validation enhancement (optional)
            const form = document.getElementById('order-form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const inputs = form.querySelectorAll('input[required]');
                    let isValid = true;
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            isValid = false;
                        }
                    });
                    if (!isValid) {
                        e.preventDefault();
                        alert('Please fill in all required fields.');
                    }
                });
            }
        });
    </script>
</body>
</html>
