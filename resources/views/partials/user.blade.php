<li class="dropdown">
    <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <!-- <img alt="image" src="/stisla/dist/assets/img/avatar/avatar-1.png" class="rounded-circle mr-1"> -->
        <i class="fas fa-user-tie mr-3"></i>
        <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->name }}</div>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-title">Informasi</div>
        <a href="" class="dropdown-item has-icon">
            <i class="far fa-user"></i> Profile
        </a>
        <div class="dropdown-divider"></div>
        <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</li>