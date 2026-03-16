<header class="header">
    <div style="display: flex; align-items: center;">

        <button id="sidebarToggle" class="hamburger-btn">
            ☰
        </button>

        <div>
            <h3 style="margin: 0; font-size: 18px;">Halo, {{ auth()->user()->name }} 👋</h3>
            <small style="color: var(--text-muted); text-transform: capitalize;">Role: {{ auth()->user()->role }}</small>
        </div>
    </div>

    <div>
        <a href="/logout" style="text-decoration: none; background: #EF4444; color: white; padding: 8px 16px; border-radius: 6px; font-weight: 500; transition: 0.2s;">Keluar</a>
    </div>
</header>
