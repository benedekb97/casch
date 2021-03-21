<div class="card">
    <div class="card-header">
        <h5 class="card-title card-top">Navigáció</h5>
    </div>
    <div class="card-body">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('admin.index') }}" class="nav-link @yield('admin.index.active')">Főoldal</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.games.index') }}" class="nav-link @yield('admin.games.active')">Játékok</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.games.endAll') }}" class="nav-link bg-danger text-danger">Összes játék befejezése</a>
            </li>
        </ul>
    </div>
</div>
