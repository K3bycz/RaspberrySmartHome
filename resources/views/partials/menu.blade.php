<nav class="sidebar-menu">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="icon-dashboard"></i> Dashboard
            </a>
        </li>
    </ul>
</nav>

<style>
    .sidebar-menu {
        background-color: #f8f9fa;
        min-height: calc(100vh - 56px);
        padding: 20px 0;
    }
    .sidebar-menu .nav-link {
        color: #333;
        padding: 12px 20px;
        margin-bottom: 5px;
    }
    .sidebar-menu .nav-link:hover {
        background-color: #e9ecef;
    }
    .sidebar-menu .nav-link.active {
        background-color: #007bff;
        color: white;
    }
</style>