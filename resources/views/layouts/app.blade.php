<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="ARPS — Academics, Researchers, and Practitioners Society">
    <title>@yield('title', 'ARPS') | Academics, Researchers, and Practitioners Society</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">

    <link rel="stylesheet" href="{{ asset('vendors/simplebar/css/simplebar.css') }}">
    <link href="{{ asset('vendors/@coreui/icons/css/free.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/color-modes.js') }}"></script>

    @stack('styles')
</head>
<body class="public-site">

    @include('partials.public.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.public.footer')

    <script src="{{ asset('vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>

    @stack('scripts')
</body>
</html>