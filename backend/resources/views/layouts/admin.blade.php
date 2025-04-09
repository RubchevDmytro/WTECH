<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #a52a2a;
            padding: 10px;
            color: white;
            position: relative;
        }
        .menu-icon, .logo, .cart-btn {
            font-size: 20px;
            margin: 0 10px;
            cursor: pointer;
        }
        input[type="text"] {
            flex: 1;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .search-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }
        .admin-container {
            position: relative;
            display: inline-block;
            cursor: pointer;
            padding: 10px;
        }
        .admin-popup {
            display: none;
            position: absolute;
            top: 35px;
            right: 0;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            min-width: 100px;
        }
        .admin-popup a {
            text-decoration: none;
            color: black;
            display: block;
            padding: 5px;
            font-weight: bold;
        }
        .admin-popup a:hover {
            background: lightgray;
        }
        .admin-container.active .admin-popup {
            display: block;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <header>
        <div class="top-bar">
            <div class="menu-icon">☰</div>
            <a href="{{ route('main_page') }}" class="logo">🏠</a>
            <input type="text" placeholder="Search...">
            <button class="search-btn">🔍</button>

            <div class="admin-container" onclick="togglePopup(event)">
                <span>Admin ▼</span>
                <div class="admin-popup">
                    <a href="{{ route('logout') }}">Log out</a>
                </div>
            </div>

            <a href="{{ route('cart') }}" class="cart-btn">🛒</a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <script>
        function togglePopup(event) {
            event.stopPropagation();
            document.querySelector('.admin-container').classList.toggle('active');
        }

        document.addEventListener('click', function(event) {
            let adminContainer = document.querySelector('.admin-container');
            if (!adminContainer.contains(event.target)) {
                adminContainer.classList.remove('active');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
