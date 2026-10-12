<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Medical Equipment Inventory</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-shell">
    <input type="checkbox" id="nav-toggle" class="nav-toggle">

    @include('partials.nav')

    <div class="app-main">
        <label for="nav-toggle" class="nav-toggle-label btn btn-secondary">Menu</label>

        <div class="app-topbar">
            <div>
                <h1>@yield('page-title')</h1>
                @hasSection('page-subtitle')
                    <p class="page-subtitle">@yield('page-subtitle')</p>
                @endif
            </div>

            <div class="page-actions">
                @yield('page-actions')
            </div>
        </div>

        <div class="app-content">
            @include('partials.flash')
            @yield('content')
        </div>
    </div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
