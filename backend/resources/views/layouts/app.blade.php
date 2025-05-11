<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Obchod s hudobnými nástrojmi')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
    <style>
        /* Стили для выпадающего списка категорий */
        .category-dropdown {
            position: relative;
            display: inline-block;
        }

        .category-btn {
            background-color: #f0f0f0;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        .category-btn:hover {
            background-color: #e0e0e0;
        }

        .category-menu {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            top: 100%;
            left: 0;
            min-width: 150px;
        }

        .category-menu a {
            display: block;
            padding: 8px 12px;
            color: #333;
            text-decoration: none;
        }

        .category-menu a:hover {
            background-color: #f0f0f0;
        }

        .category-menu.show {
            display: block;
        }
    </style>
</head>
<body>
<header>
    <div class="navbar-wrapper">
        <nav class="navbar">
            <div class="logo">
                <a href="{{ route('main_page') }}" class="logo-link">🏠</a>
            </div>
            <div class="search-form">
                <form method="GET" action="{{ route('main_page') }}" class="search-form">
                    <div class="autocomplete-wrapper">
                        <input type="text" placeholder="Hľadať..." name="search" id="search-input" value="{{ request()->query('search') }}" autocomplete="off">
                        <div id="autocomplete-suggestions" class="autocomplete-suggestions"></div>
                    </div>
                    <button type="submit" class="search-btn">🔍</button>
                </form>
            </div>
            <!-- Добавляем выпадающий список категорий -->
            <div class="category-dropdown">
                <button class="category-btn">Kategórie</button>
                <div class="category-menu">
                    @if(isset($categories) && $categories->isNotEmpty())
                        @foreach($categories as $category)
                            <a href="{{ route('main_page', ['category' => $category->id]) }}">{{ $category->name }}</a>
                        @endforeach
                    @else
                        <a href="{{ route('main_page') }}">Žiadne kategórie</a>
                    @endif
                </div>
            </div>
            <div class="nav-right">
                <!-- Показываем корзину всем пользователям -->
                <a href="{{ route('cart') }}" class="cart-link {{ Request::is('cart') ? 'active' : '' }}">🛒 Košík</a>
                @auth
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.menu') }}" class="nav-item {{ Request::is('admin*') ? 'active' : '' }}">Admin Panel</a>
                    @endif
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        @method('POST')
                        <button type="submit" class="nav-item logout-btn">Odhlásiť sa</button>
                    </form>
                @else
                    <a href="{{ route('login.form') }}" class="nav-item {{ Request::is('login*') ? 'active' : '' }}">Prihlásiť sa</a>
                @endif
            </div>
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer>
    <p>© 2025 Store | Sledujte nás na sociálnych sieťach | O nás</p>
</footer>

<script src="{{ asset('js/main_page.js') }}"></script>
<script>
    // JavaScript для управления выпадающим списком категорий
    document.addEventListener("DOMContentLoaded", function () {
        const categoryBtn = document.querySelector(".category-btn");
        const categoryMenu = document.querySelector(".category-menu");

        categoryBtn.addEventListener("click", function () {
            categoryMenu.classList.toggle("show");
        });

        // Закрытие меню при клике вне его
        document.addEventListener("click", function (e) {
            if (!categoryBtn.contains(e.target) && !categoryMenu.contains(e.target)) {
                categoryMenu.classList.remove("show");
            }
        });
    });
</script>
</body>
</html>
