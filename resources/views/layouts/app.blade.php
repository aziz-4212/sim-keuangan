<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts._partials._head')
</head>

<body class="hold-transition layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        @include('layouts._partials._navbar')
        {{-- @include('layouts._partials._sidebar') --}}
        {{-- ===========overlay============= --}}
        <style>
            /* CSS untuk kelas overlay dan hidden */
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            .hidden {
                display: none;
            }
        </style>
        <div class="overlay hidden">
            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
        {{-- ===========end overlay============= --}}
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="margin-left:0px;">
            @yield('content')
        </div>
        @include('layouts._partials._footer')
    </div>
    <!-- ./wrapper -->
    @include('layouts._partials._js')
</body>

</html>
