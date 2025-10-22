<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    </head>
    <body>
        <div id="app">
            <div class="main-wrapper main-wrapper-1">
                <div class="navbar-bg"></div>
                <nav class="navbar navbar-expand-lg main-navbar">
                    <div class="form-inline mr-auto">
                        <ul class="navbar-nav mr-3">
                            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                        </ul>
                    </div>
                    <ul class="navbar-nav navbar-right">
                        {{-- @include('partials.pesan') --}}
                        @include('partials.notifikasi')
                        @include('partials.user')
                    </ul>
                </nav>
                <div class="main-sidebar sidebar-style-2">
                    <aside id="sidebar-wrapper">
                        <div class="sidebar-brand">
                            <a href="">Qr-Code Absensi</a>
                        </div>
                        <div class="sidebar-brand sidebar-brand-sm">
                            <a href="">QR-A</a>
                        </div>
                        @include('partials.sidebar')
                    </aside>
                </div>
                <!-- Main Content -->
                <div class="main-content">
                    @yield('content')
                </div>
                @include('partials.footer')
            </div>
        </div>
        @include('partials.script')
        @stack('scripts')
    </body>
</html>