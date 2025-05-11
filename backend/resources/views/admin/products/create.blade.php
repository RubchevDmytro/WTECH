<!DOCTYPE html>
   <html lang="en">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Create Product</title>
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

           <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
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
                   <label for="image">Product Image:</label>
                   <input type="file" name="image" id="image" accept="image/*">
               </div>
               <button type="submit" class="btn">Create Product</button>
           </form>

           <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
       </main>

       <footer>
           <p>© 2025 Store | Admin Panel</p>
       </footer>
   </body>
   </html>
