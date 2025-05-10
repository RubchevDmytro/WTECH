<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <header>
        <div class="top-bar">
            <a href="{{ route('admin.menu') }}" class="logo">Admin Panel</a>
            <a href="{{ route('logout') }}" class="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{ Auth::user()->email }} (Log Out)
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </header>

    <main>
        <h1>Manage Products</h1>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('products.create') }}" class="btn">Create New Product</a>

        @if($products->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Rating</th>
                        <th>Image</th>
                        <th>Subcategory</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->price }} €</td>
                            <td>{{ str_repeat('⭐', $product->rating) . str_repeat('☆', 5 - $product->rating) }}</td>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 100px;">
                                @else
                                    No image
                                @endif
                            </td>
                            <td>{{ $product->subcategory->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('products.edit', $product) }}" class="btn">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination" id="pagination">
                {{ $products->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        @else
            <p>No products found.</p>
        @endif
    </main>

    <footer>
        <p>© 2025 Store | Admin Panel</p>
    </footer>
</body>
</html>
