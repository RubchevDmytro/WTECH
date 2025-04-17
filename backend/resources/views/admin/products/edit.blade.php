<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
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
        <h1>Edit Product: {{ $product->name }}</h1>

        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required>
            </div>
            <div class="form-group">
                <label for="price">Price (€):</label>
                <input type="number" name="price" id="price" step="0.01" value="{{ old('price', $product->price) }}" required>
            </div>
            <div class="form-group">
                <label for="rating">Rating (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" value="{{ old('rating', $product->rating) }}" required>
            </div>
            <div class="form-group">
                <label for="subcategory_id">Subcategory:</label>
                <select name="subcategory_id" id="subcategory_id" required>
                    <option value="">Select a subcategory</option>
                    @foreach($categories as $category)
                        <optgroup label="{{ $category->name }}">
                            @foreach($category->subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}" {{ old('subcategory_id', $product->subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                    {{ $subcategory->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="image">Product Image:</label>
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 100px;">
                    <p>Upload a new image to replace the current one.</p>
                @endif
                <input type="file" name="image" id="image" accept="image/*">
            </div>
            <button type="submit" class="btn">Update Product</button>
        </form>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
    </main>

    <footer>
        <p>© 2025 Store | Admin Panel</p>
    </footer>
</body>
</html>
