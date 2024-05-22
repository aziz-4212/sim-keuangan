<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts._partials._head')
</head>

<body>
    <div class="page">
        @include('layouts._partials._navbar')
        <div class="page-wrapper">
            {{-- Use regular Blade syntax to yield content --}}
            @yield('content')
        </div>
    </div>
    @include('layouts._partials._js')
</body>

</html>
