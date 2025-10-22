<ul class="sidebar-menu">
    <li class="menu-header">Beranda</li>

    <!-- Route Dashboard Berdasarkan Role-->
    @if (Auth::user()->role == 'Admin')
        <li class="{{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
            <a href="{{ route('dashboard.admin') }}" class="nav-link">
                <i class="fas fa-fire"></i>
                <span>Dashboard</span>
            </a>
        </li>        
    @else    
        <li class="{{ request()->routeIs('dashboard.user') ? 'active' : '' }}">
            <a href="{{ route('dashboard.user') }}" class="nav-link">
                <i class="fas fa-fire"></i>
                <span>Dashboard</span>
            </a>
        </li>
    @endif
    <li class="menu-header">Menu Navigasi</li>

    <!-- Menu Navigasi User Berdasarkan Role -->
    @if (Auth::user()->role == 'Admin')
        <li @class(['active' => request()->routeIs('admin.dataUser')])>
            <a class="nav-link" href="{{ route('admin.dataUser') }}">
                <i class="fas fa-user"></i> 
                <span>Data User</span>
            </a>
        </li>
         <li @class(['active' => request()->routeIs('admin.generate-qr') ||  request()->routeIs('admin.generate-qr.show')])>
            <a class="nav-link" href="{{ route('admin.generate-qr') }}">
                <i class="fas fa-qrcode"></i> 
                <span>QR-Code Absensi</span>
            </a>
        </li>
        <li @class(['active' => request()->routeIs('login')])>
            <a class="nav-link" href="">
                <i class="far fa-square"></i> 
                <span>Rekap Absen</span>
            </a>
        </li>
    @else
        <li @class(['active' => request()->routeIs('user.scanQR')])>
            <a class="nav-link" href="{{ route('user.scanQR') }}">
                <i class="fas fa-qrcode"></i> 
                <span>QR-Code Absensi</span>
            </a>
        </li>
    @endif
</ul>
{{-- <li class="dropdown">
    <a href="" class="nav-link has-dropdown">
        <i class="fas fa-th"></i> 
        <span>Absen</span>
    </a>
    <ul class="dropdown-menu">
        <li><a class="nav-link" href="">Daftar Absen</a></li>
        <li><a class="nav-link" href="">Rekap Absen</a></li>
    </ul>
</li> --}}