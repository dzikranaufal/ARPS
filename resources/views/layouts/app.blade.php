<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>@yield('title', 'ARPS') | Academics, Researchers, and Practitioners Society</title>
    <meta name="description" content="@yield('meta_description', 'ARPS — Academics, Researchers, and Practitioners Society')">
    <meta property="og:title" content="@yield('title', 'ARPS')">
    <meta property="og:description" content="@yield('meta_description', 'ARPS — Academics, Researchers, and Practitioners Society')">
    <meta property="og:type" content="website">
    @yield('meta')

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