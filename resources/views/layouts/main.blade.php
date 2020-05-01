<!DOCTYPE html>
<html lang="hu">
    <head>
        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-reboot.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/main.css') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    </head>
    <body>
    <div class="container">
        <h1 class="page-header"><a style="text-decoration:none; color:white;" href="{{ route('index') }}">Cards Against <span class="sch">Schönherz</span></a></h1>
        @yield('content')
        <div class="row justify-content-center">
            <div class="col-md-3" style="text-align:center;">
                <a href="{{ route('disclaimer') }}">Dis<b><i><u>c</u></i></b>laimer</a> @auth | <a href="{{ route('report') }}">Hibajelentés</a> @endauth | <a target="_blank" href="{{ route('help') }}">Segítség</a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.0.min.js"></script>
    <script src="https://js.pusher.com/5.1/pusher.min.js"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/all.js') }}"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
    @stack('modals')
    @stack('scripts')
    </body>
</html>
