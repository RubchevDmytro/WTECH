@extends('layouts.app')

@section('title', "$product->name")

@section('content')
    <section class="products">
        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <div class="product-details">
            <!-- Изображения продукта -->
            <div class="product-images">
                <div class="main-image">
                    @if ($product->primary_image)
                        <img src="data:{{ $product->primary_image->mime_type }};base64,{{ $product->primary_image->image_data }}" alt="{{ $product->name }}">
                    @endif
                </div>
                <div class="thumbnail-images">
                    @foreach ($product->images as $image)
                        <img src="data:{{ $image->mime_type }};base64,{{ $image->image_data }}" alt="Thumbnail" onclick="document.querySelector('.main-image img').src=this.src">
                    @endforeach
                </div>
            </div>

            <!-- Информация о продукте -->
            <div class="product-info">
                <h3>{{ $product->name }}</h3>
                <p class="rating">{{ str_repeat('⭐', $product->rating) . str_repeat('☆', 5 - $product->rating) }}</p>
                <p><strong>Description:</strong> {{ $product->description ?? 'No description available.' }}</p>
                <p><strong>Stock:</strong> {{ $product->stock ?? 0 }}</p>
                <p><strong>Category:</strong> {{ $product->category ? $product->category->name : 'N/A' }}</p>
            </div>

            <!-- Действия с продуктом -->
            <div class="product-actions">
                <div class="price">{{ $product->price }} €</div>
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <div class="quantity-control">
                        <button type="button" onclick="this.nextElementSibling.stepDown()">-</button>
                        <input type="number" name="quantity" value="1" min="1" class="quantity">
                        <button type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                    </div>
                    <button type="submit" class="add-to-cart-btn">Add To Cart</button>
                </form>
            </div>
        </div>

        <a href="{{ route('main_page') }}" class="back-link">Back to Products</a>
    </section>
@endsection

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

    /* Адаптивность */
    @media (max-width: 768px) {
        .product-details {
            flex-direction: column;
            margin: 0 10px;
            padding: 15px;
        }

        .main-image img {
            max-width: 100%;
        }

        .thumbnail-images img {
            width: 50px;
            height: 50px;
        }

        .product-actions {
            padding: 15px;
        }
    }
</style>
