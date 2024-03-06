<nav class="main-header navbar navbar-expand-md navbar-light navbar-maroon" style="margin-left: 0px;">
    <span class="brand-text font-weight-light"><img src="{{ asset('img/logo-rsi.png') }}" width="30" alt=""></span>
    <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="{{route('home')}}" class="nav-link text-white text-bold">Home</a>
            </li>
            <li class="nav-item">
                <a href="{{route('laporan.index')}}" class="nav-link text-white text-bold">Laporan</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <p class="nav-link text-white text-bold">{{auth()->user()->user_log->USFULLNM}}</p>
            </li>
            <li class="nav-item">
                <a href="{{ route('logout') }}" class="nav-link text-white">Logout <i class="fas fa-sign-out-alt"></i></a>
            </li>
        </ul>
    </div>
</nav>
