<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
</head>
<body>
    <h1>Your Orders</h1>
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    @forelse($orders as $order)
        <div>
            <h3>Order #{{ $order->id }}</h3>
            <p>Date: {{ $order->date }}</p>
            <p>Shipping Address: {{ $order->shipping_address }}</p>
            <p>Total Price: {{ $order->total_price }} €</p>
            <h4>Items:</h4>
            @foreach($order->orderDetails as $detail)
                <p>{{ $detail->product->name }} - Quantity: {{ $detail->quantity }} - Price: {{ $detail->price }} €</p>
            @endforeach
        </div>
    @empty
        <p>You have no orders.</p>
    @endforelse

    <a href="{{ route('main_page') }}">Back to Main Page</a>
</body>
</html>
