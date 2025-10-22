<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>Halaman &mdash; Login</title>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="/stisla/dist/assets/modules/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="/stisla/dist/assets/modules/fontawesome/css/all.min.css">
        <link rel="stylesheet" href="/stisla/dist/assets/modules/bootstrap-social/bootstrap-social.css">
        <link rel="stylesheet" href="/stisla/dist/assets/css/style.css">
        <link rel="stylesheet" href="/stisla/dist/assets/css/components.css">
        <style>
            .card-header {
                display: flex;
                justify-content: center;
                align-items: center;          
            }
        </style>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-94034622-3');
        </script>
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    </head>

    <body>
        <div id="app">
            <section class="section">
                <div class="container mt-5 overflow-x-hidden">
                    <div class="row">
                        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                            <div class="card card-primary">
                                <div class="card-header"><h4>Login</h4></div>
                                <div class="card-body">
                                    @error('loginError')
                                        <div id="alert" class="alert alert-danger alert-block" style="text-align:center">
                                            <strong>Perhatian!</strong>
                                            <p style="text-align:center">{{$message}}</p>
                                        </div>
                                    @enderror

                                    @if($errors->has('loginAkses'))
                                        <div id="alert" class="alert alert-danger alert-block" style="text-align:center">
                                            <strong>Akses Halaman Dilarang</strong>
                                            <p style="text-align:center">{{ $errors->first('loginAkses') }}</p>
                                        </div>
                                    @endif
                                        
                                    <form method="POST" action="{{ route('auth.login') }}" class="needs-validation">
                                        @csrf
                                        <div class="form-group">
                                            <label for="text">Username or Kode User</label>
                                            <input type="text" class="form-control" id="text" name="text" placeholder="Masukkan Username atau Kode User anda" value="{{ old('text') }}">
                                            @error('text')
                                                <small style="color:red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <div class="d-block">
                                                <label for="password" class="control-label">Password</label>
                                                <div class="float-right">
                                                    <a href="" class="text-small">Forgot Password?</a>
                                                </div>
                                            </div>
                                            <input id="password" type="password" class="form-control" name="password" placeholder="Masukkan password anda">
                                            @error('password')
                                                <small style="color:red">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Login</button>
                                        </div>
                                    </form>
                                    <div class="text-center mt-3 mb-3">
                                        <div class="text-job text-muted">Login With</div>
                                    </div>
                                    <div class="row sm-gutters">
                                        <div class="col-6">
                                            <a class="btn btn-block btn-social btn-google">
                                                <span class="fab fa-google"></span> Google
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a class="btn btn-block btn-social btn-github">
                                                <span class="fab fa-github"></span> Github
                                            </a>                                
                                        </div>
                                    </div>
                                    <div class="form-group mt-4">
                                        <button onclick="window.location.href='{{ route('beranda') }}'" type="button" class="btn btn-success btn-lg btn-block" tabindex="4">Beranda</button>
                                    </div>
                                    <div class="mt-4 mb-2 text-muted text-center">
                                        Don't have an account? <a href="auth-register.html">Create One</a>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="simple-footer">
                                Copyright &copy; Stisla {{ date('Y') }}
                            </div> --}}
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Custom Scripts Alert -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var successAlert = document.getElementById('alert');
                    if (successAlert) {
                        successAlert.style.transition = 'opacity 0.5s ease-out';
                        successAlert.style.opacity = '0';
                        setTimeout(function() {
                            successAlert.remove();
                        }, 500);
                    }
                }, 3000);
            });
        </script>

        <!-- General JS Scripts -->
        <script src="/stisla/dist/assets/modules/jquery.min.js"></script>
        <script src="/stisla/dist/assets/modules/popper.js"></script>
        <script src="/stisla/dist/assets/modules/tooltip.js"></script>
        <script src="/stisla/dist/assets/modules/bootstrap/js/bootstrap.min.js"></script>
        <script src="/stisla/dist/assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
        <script src="/stisla/dist/assets/modules/moment.min.js"></script>
        <script src="/stisla/dist/assets/js/stisla.js"></script>
        <script src="/stisla/dist/assets/js/scripts.js"></script>
        <script src="/stisla/dist/assets/js/custom.js"></script>
    </body>
</html>