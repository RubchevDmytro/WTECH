@extends('layouts.admin')

@section('title', isset($product) ? 'Upraviť produkt' : 'Pridať tovar')

@section('styles')
    <style>
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            background: #ddd;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            margin: 20px auto;
        }
        .upload-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        #upload-boxes {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .upload-box {
            width: 150px;
            height: 150px;
            background: gray;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border-radius: 5px;
            text-align: center;
            position: relative;
        }
        .upload-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 5px;
        }
        #add-image-btn {
            width: 30px;
            height: 30px;
            background: #a19292;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f3e5e5;
            padding: 20px;
            border-radius: 5px;
        }
        input, textarea, select {
            width: 200px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .submit-btn {
            background: #a19292;
            border: none;
            padding: 10px;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .submit-btn:hover {
            background: #8a7a7a;
        }
    </style>
@endsection

@section('content')
    <div class="form-container">
        <div class="upload-container">
            <div id="upload-boxes">
                @if(isset($product) && $product->images->count() > 0)
                    @foreach($product->images as $image)
                        <div class="upload-box" data-id="{{ $image->id }}">
                            <img src="data:{{ $image->mime_type }};base64,{{ base64_encode($image->image_data) }}" alt="Image">
                            <input type="file" name="images[]" style="display: none;" accept="image/*">
                        </div>
                    @endforeach
                @else
                    <div class="upload-box">
                        Upload File <br> (JPG, PNG)
                        <input type="file" name="images[]" style="display: none;" accept="image/*">
                    </div>
                @endif
            </div>
            <button type="button" id="add-image-btn">+</button>
        </div>

        <div class="form-group">
            <form method="POST" action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" enctype="multipart/form-data">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <label for="nazov">Nazov</label>
                <input type="text" id="nazov" name="name" value="{{ old('name', isset($product) ? $product->name : '') }}" required>
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror

                <label for="mnozstvo">Množstvo</label>
                <input type="number" id="mnozstvo" name="left_stock" value="{{ old('left_stock', isset($product) ? $product->left_stock : '') }}" required>
                @error('left_stock')
                    <span class="error">{{ $message }}</span>
                @enderror

                <label for="popis">Popis</label>
                <textarea id="popis" name="description">{{ old('description', isset($product) ? $product->description : '') }}</textarea>
                @error('description')
                    <span class="error">{{ $message }}</span>
                @enderror

                <label for="price">Cena (€)</label>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price', isset($product) ? $product->price : '') }}" required>
                @error('price')
                    <span class="error">{{ $message }}</span>
                @enderror

                <label for="rating">Hodnotenie (0-5)</label>
                <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" value="{{ old('rating', isset($product) ? $product->rating : '') }}" required>
                @error('rating')
                    <span class="error">{{ $message }}</span>
                @enderror

                <label for="category_id">Kategória</label>
                <select id="category_id" name="category_id" required>
                    @foreach(\App\Models\Category::all() as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', isset($product) ? $product->category_id : '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="error">{{ $message }}</span>
                @enderror

                <button type="submit" class="submit-btn">Pridať tovar</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById("add-image-btn").addEventListener("click", function() {
            let uploadContainer = document.getElementById("upload-boxes");
            let newUploadBox = document.createElement("div");
            newUploadBox.classList.add("upload-box");
            newUploadBox.innerHTML = `Upload File <br> (JPG, PNG) <input type="file" name="images[]" style="display: none;" accept="image/*">`;
            uploadContainer.appendChild(newUploadBox);
            attachFileInputListener(newUploadBox);
        });

        // Привязываем обработчик к существующим upload-box
        document.querySelectorAll('.upload-box').forEach(box => attachFileInputListener(box));

        function attachFileInputListener(box) {
            let fileInput = box.querySelector('input[type="file"]');
            fileInput.addEventListener('change', function(event) {
                let file = event.target.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        box.innerHTML = `<img src="${e.target.result}" alt="Image">`;
                        box.appendChild(fileInput); // Возвращаем input обратно
                    };
                    reader.readAsDataURL(file);
                }
            });

            box.addEventListener('click', function() {
                fileInput.click();
            });
        }
    </script>
@endsection
