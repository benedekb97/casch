<!DOCTYPE html>
<html lang="hu">
<head>
    <title>casch-admin &raquo; @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-reboot.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
</head>
<body>
<div class="container">
    <h1 class="page-header with-description"><a style="text-decoration:none; color:white;" href="{{ route('index') }}">Cards Against <span class="sch">Schönherz</span></a></h1>
    <h2 class="page-description">Admin felület</h2>
    <div class="row">
        <div class="col-lg-3" style="margin-bottom:15px;">
            @include('includes.admin.nav')
        </div>
        <div class="col-lg-9">
            @yield('content')
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.0.min.js"></script>
<script src="https://js.pusher.com/5.1/pusher.min.js"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/all.js') }}"></script>
@stack('modals')
@stack('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>
</body>
</html>
