@extends('layouts.admin')

@section('title', 'Manage Products')

@section('styles')
    <style>
        .container {
            width: 80%;
            margin: 20px auto;
        }
        .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ccc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .name, .description {
            flex: 1;
            padding: 10px;
            background: #aaa;
            border-radius: 5px;
            text-align: center;
            margin: 0 10px;
        }
        .icons {
            display: flex;
            gap: 10px;
        }
        .icons span {
            font-size: 18px;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        @foreach($products as $product)
            <div class="item" data-id="{{ $product->id }}">
                @php
                    $primaryImage = $product->primaryImage();
                    // Проверяем, что image_data — это строка
                    $imageSrc = $primaryImage && isset($primaryImage->image_data) && is_string($primaryImage->image_data)
                        ? 'data:' . ($primaryImage->mime_type ?? 'image/jpeg') . ';base64,' . base64_encode($primaryImage->image_data)
                        : asset('images/box.png');
                @endphp
                <img src="{{ $imageSrc }}" alt="{{ $product->name }}">
                <div class="name">{{ $product->name }}</div>
                <div class="description">{{ $product->description }}</div>
                <div class="icons">
                    <span class="edit-btn">⚙️</span>
                    <span class="delete-btn">🗑</span>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".edit-btn").forEach(editIcon => {
                editIcon.addEventListener("click", function() {
                    let productId = this.closest(".item").dataset.id;
                    window.location.href = `{{ url('products') }}/${productId}/edit`;
                });
            });

            document.querySelectorAll(".delete-btn").forEach(trashIcon => {
                trashIcon.addEventListener("click", function() {
                    if (confirm('Naozaj chcete odstrániť tento produkt?')) {
                        let productId = this.closest(".item").dataset.id;
                        fetch(`{{ url('products') }}/${productId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.closest(".item").remove();
                                alert('Produkt bol odstránený.');
                            } else {
                                alert('Chyba pri odstraňovaní produktu.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Chyba pri odstraňovaní produktu.');
                        });
                    }
                });
            });
        });
    </script>
@endsection
