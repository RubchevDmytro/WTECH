<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <style>
        .progress {
            width: 100%;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin-top: 10px;
        }
        .progress-bar {
            width: 0;
            height: 20px;
            background-color: #4CAF50;
            border-radius: 5px;
            text-align: center;
            color: white;
            transition: width 0.3s;
        }
        .image-input-group {
            margin-bottom: 10px;
        }
    </style>
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
        <h1>Create New Product</h1>

        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="price">Price (€):</label>
                <input type="number" name="price" id="price" step="0.01" value="{{ old('price') }}" required>
            </div>
            <div class="form-group">
                <label for="rating">Rating (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" value="{{ old('rating') }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="5" required>{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label for="stock">Stock Left:</label>
                <input type="number" name="stock" id="stock" min="0" value="{{ old('stock') }}" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id" required>
                    <option value="">Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Product Images:</label>
                <div id="imageInputs">
                    <div class="image-input-group">
                        <input type="file" name="images[0]" id="images_0" accept="image/*">
                        <div id="preview_0"></div>
                        <input type="checkbox" name="is_primary[0]" id="is_primary_0" value="1" checked> Primary
                        <label for="is_primary_0"> Primary</label><br>
                    </div>
                </div>
                <button type="button" id="addImage" class="btn">Add Another Image</button>
                <div class="progress" id="progressBarContainer" style="display: none;">
                    <div class="progress-bar" id="progressBar">0%</div>
                </div>
            </div>
            <button type="submit" class="btn">Create Product</button>
        </form>

        <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
    </main>

    <footer>
        <p>© 2025 Store | Admin Panel</p>
    </footer>

    <script>
        let imageIndex = 1;

        document.getElementById('addImage').addEventListener('click', function() {
            const imageInputs = document.getElementById('imageInputs');
            const newInputGroup = document.createElement('div');
            newInputGroup.className = 'image-input-group';
            newInputGroup.innerHTML = `
                <input type="file" name="images[${imageIndex}]" id="images_${imageIndex}" accept="image/*">
                <div id="preview_${imageIndex}"></div>
                <input type="checkbox" name="is_primary[${imageIndex}]" id="is_primary_${imageIndex}" value="1"> Primary
                <label for="is_primary_${imageIndex}"> Primary</label><br>
            `;
            imageInputs.appendChild(newInputGroup);
            imageIndex++;

            // Добавляем обработчик для нового поля
            const newInput = newInputGroup.querySelector('input[type="file"]');
            newInput.addEventListener('change', function(e) {
                const previewId = `preview_${imageIndex - 1}`;
                const previewContainer = document.getElementById(previewId);
                previewContainer.innerHTML = '';
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const img = document.createElement('img');
                        img.src = event.target.result;
                        img.style.maxWidth = '100px';
                        img.style.marginRight = '10px';
                        previewContainer.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        document.getElementById('productForm').addEventListener('submit', function(e) {
            const progressBar = document.getElementById('progressBar');
            const progressBarContainer = document.getElementById('progressBarContainer');
            progressBarContainer.style.display = 'block';
            let progress = 0;
            const interval = setInterval(() => {
                if (progress < 100) {
                    progress += 10;
                    progressBar.style.width = progress + '%';
                    progressBar.textContent = progress + '%';
                } else {
                    clearInterval(interval);
                }
            }, 200);
        });

        // Инициализация обработчиков для первого поля
        document.getElementById('images_0').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('preview_0');
            previewContainer.innerHTML = '';
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.maxWidth = '100px';
                    img.style.marginRight = '10px';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
