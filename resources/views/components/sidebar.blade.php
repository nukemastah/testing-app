<!-- Sidebar Component -->
<aside class="sidebar-wrapper">
    <style>
        .sidebar-wrapper {
            width: 250px;
            background: linear-gradient(to bottom, #b4746f, #8b5a57);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar-brand {
            padding: 20px 25px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-brand h3 {
            color: white;
            font-size: 1.2rem;
            margin: 0;
            font-weight: bold;
        }

        .sidebar-menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar-menu > li {
            margin: 0;
            position: relative;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: rgba(0, 0, 0, 0.2);
            border-left-color: #d4a574;
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background-color: #d4a574;
            color: #333;
            border-left-color: #d4a574;
            transform: translateX(5px);
        }

        .sidebar-menu .icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-menu .menu-label {
            flex: 1;
        }

        .sidebar-menu .toggle-icon {
            margin-left: auto;
            transition: transform 0.3s ease;
            display: inline-block;
            width: 16px;
            height: 16px;
        }

        .sidebar-menu .submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar-menu > li.has-submenu.open .submenu {
            max-height: 500px;
        }

        .sidebar-menu > li.has-submenu.open .toggle-icon {
            transform: rotate(180deg);
        }

        .sidebar-menu .submenu a {
            padding-left: 60px;
            font-size: 13px;
            font-weight: 400;
            border-left: 2px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar-menu .submenu a:hover {
            border-left-color: #d4a574;
            background-color: rgba(0, 0, 0, 0.3);
        }

        .sidebar-menu .submenu a.active {
            background-color: rgba(212, 165, 116, 0.3);
            color: #d4a574;
            border-left-color: #d4a574;
        }
    </style>

    <div class="sidebar-brand">
        <h3>📊 Dashboard</h3>
    </div>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) active @endif">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span class="menu-label">Dashboard</span>
            </a>
        </li>

        <!-- Master Dropdown -->
        <li class="has-submenu @if(request()->routeIs('master.*') || request()->routeIs('barang*') || request()->routeIs('pemasok*') || request()->routeIs('pelanggan*') || request()->routeIs('rekening*') || request()->routeIs('pengguna*')) open @endif">
            <a href="#" onclick="toggleMenu(this); return false;">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span class="menu-label">Master</span>
                <span class="toggle-icon">▼</span>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('barang.index') }}" class="@if(request()->routeIs('barang*')) active @endif">🏷️ Barang</a></li>
                <li><a href="{{ route('pemasok.index') }}" class="@if(request()->routeIs('pemasok*')) active @endif">🏭 Pemasok</a></li>
                <li><a href="{{ route('pelanggan.index') }}" class="@if(request()->routeIs('pelanggan*')) active @endif">👥 Pelanggan</a></li>
            </ul>
        </li>

        <!-- Transaksi Dropdown -->
        <li class="has-submenu @if(request()->routeIs('biaya*') || request()->routeIs('pindah*') || request()->routeIs('penjualan*') || request()->routeIs('pembayaran*')) open @endif">
            <a href="#" onclick="toggleMenu(this); return false;">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="3" y1="15" x2="21" y2="15"></line>
                </svg>
                <span class="menu-label">Transaksi</span>
                <span class="toggle-icon">▼</span>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('pembayaran-pembelian.index') }}" class="@if(request()->routeIs('pembayaran-pembelian*')) active @endif">💰 Pembayaran Pembelian</a></li>
                <li><a href="{{ route('penjualan.index') }}" class="@if(request()->routeIs('penjualan*')) active @endif">🛒 Penjualan</a></li>
                <li><a href="{{ route('pembayaran-penjualan.index') }}" class="@if(request()->routeIs('pembayaran-penjualan*')) active @endif">🧾 Pembayaran Penjualan</a></li>
            </ul>
        </li>

        <!-- Laporan Dropdown -->
        <li class="has-submenu @if(request()->routeIs('laporan.*')) open @endif">
            <a href="#" onclick="toggleMenu(this); return false;">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                    <line x1="9" y1="19" x2="15" y2="19"></line>
                </svg>
                <span class="menu-label">Laporan</span>
                <span class="toggle-icon">▼</span>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('laporan.mutasiRekening') }}" class="@if(request()->routeIs('laporan.mutasiRekening')) active @endif">📊 Mutasi Rekening</a></li>
                <li><a href="{{ route('laporan.mutasiStok') }}" class="@if(request()->routeIs('laporan.mutasiStok')) active @endif">📈 Mutasi Stok</a></li>
                <li><a href="{{ route('laporan.kas') }}" class="@if(request()->routeIs('laporan.kas')) active @endif">💵 Kas</a></li>
                <li><a href="{{ route('laporan.piutang') }}" class="@if(request()->routeIs('laporan.piutang')) active @endif">📋 Piutang</a></li>
                <li><a href="{{ route('laporan.penjualan') }}" class="@if(request()->routeIs('laporan.penjualan')) active @endif">📉 Laporan Penjualan</a></li>
            </ul>
        </li>

        <!-- Logout -->
        <li style="border-top: 1px solid rgba(255, 255, 255, 0.1); margin-top: 20px; padding-top: 20px;">
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full text-left" style="background: none; border: none; padding: 0; cursor: pointer;">
                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" style="display: flex; align-items: center; padding: 15px 25px; color: white; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.3s ease; border-left: 4px solid transparent;">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span class="menu-label">Logout</span>
                    </a>
                </button>
            </form>
        </li>
    </ul>

    <script>
        function toggleMenu(element) {
            const parentLi = element.closest('li');
            parentLi.classList.toggle('open');
        }
    </script>
</aside>
