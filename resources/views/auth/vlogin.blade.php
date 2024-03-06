<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIM Front Office | RSI Kendal</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ICO') }}" />
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        @if (session('error'))
            <div class="alert alert-danger" id="error-alert">
                {{ session('error') }}
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var errorAlert = document.getElementById('error-alert');
                    if (errorAlert) {
                        setTimeout(function() {
                            errorAlert.style.display = 'none';
                        }, 3000); // 3 detik (3000 ms)
                    }
                });
            </script>
        @endif

        <!-- /.login-logo -->
        <div class="card card-outline card-maroon">
            <div class="card-header text-center">
                <a href="" class="h1"><b>SIM Keuangan</b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Log in SIM Keuangan RSI Kendal</p>

                <form action="{{ route('loginCheck') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="USLOGNM" class="form-control" placeholder="Username"
                            autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="USPASS" class="form-control" placeholder="Password"
                            autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <!-- /.col -->
                        <div class="col-12">
                            <button type="submit" class="btn bg-maroon btn-block"><span style="color: white">Log In</span></button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
</body>

</html>
