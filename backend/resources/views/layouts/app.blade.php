<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Obchod s hudobnými nástrojmi')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Подключаем main_page.css, если стили не перенесены -->
    <link rel="stylesheet" href="{{ asset('css/main_page.css') }}">
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
                        <div class="nav-right">
                @auth
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.menu') }}" class="nav-item {{ Request::is('admin*') ? 'active' : '' }}">Admin Panel</a>
                    @endif
                    <a href="{{ route('cart') }}" class="cart-link {{ Request::is('cart') ? 'active' : '' }}">🛒 Košík</a>
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

    <script src="{{ asset('js/app.js') }}"></script>
    <!-- Подключаем main_page.js, если скрипты не перенесены -->
    <script src="{{ asset('js/main_page.js') }}"></script>
</body>
</html>
