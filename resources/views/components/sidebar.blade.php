<aside class="sidebar" id="sidebar">

    <div class="sidebar-header" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
        <span class="nav-icon">🍽️</span>
        <span class="sidebar-title">Resto POS</span>
    </div>

    <nav class="sidebar-nav">
        <div class="menu-label" style="font-size: 12px; text-transform: uppercase; margin-bottom: 15px; color: #64748B;">Menu Utama</div>

        @if(auth()->user()->role === 'admin')
        <a href="/admin/dashboard" class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="/admin/menus" class="nav-item {{ request()->is('admin/menus') ? 'active' : '' }}">
            <span class="nav-icon">📋</span>
            <span class="nav-text">Master Menu</span>
        </a>
        <a href="/admin/tables" class="nav-item {{ request()->is('admin/tables') ? 'active' : '' }}">
            <span class="nav-icon">🪑</span><span class="nav-text">Manajemen Meja</span>
        </a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'kitchen']))
        <a href="/kitchen" class="nav-item {{ request()->is('kitchen') ? 'active' : '' }}">
            <span class="nav-icon">🍳</span>
            <span class="nav-text">Layar Dapur</span>
        </a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'cashier']))
        <a href="/cashier" class="nav-item {{ request()->is('cashier') ? 'active' : '' }}">
            <span class="nav-icon">💰</span>
            <span class="nav-text">Kasir (POS)</span>
        </a>
        @endif
    </nav>
</aside>
