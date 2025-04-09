
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
</head>
<body>
    <h1>Your Cart</h1>
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif
    @forelse($cartItems as $item)
        <div>
            <h3>{{ $item->product->name }}</h3>
            <p>Quantity: {{ $item->quantity }}</p>
            <p>Price: {{ $item->product->price * $item->quantity }} €</p>
        </div>
    @empty
        <p>Your cart is empty.</p>
    @endforelse

    @if($cartItems->isNotEmpty())
        <form method="POST" action="{{ route('order.create') }}">
            @csrf
            <div class="form-group">
                <label for="shipping_address">Shipping Address:</label>
                <input type="text" id="shipping_address" name="shipping_address" required>
                @error('shipping_address')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit">Place Order</button>
        </form>
    @endif

    <a href="{{ route('main_page') }}">Back to Main Page</a>
</body>
</html>
