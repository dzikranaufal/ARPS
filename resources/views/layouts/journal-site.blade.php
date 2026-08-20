<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>@yield('title', $journal['name'] ?? 'Journal')</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link href="{{ asset('vendors/@coreui/icons/css/free.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="{{ asset('js/color-modes.js') }}"></script>

    @stack('styles')
</head>

<body class="journal-site">

    {{-- NOTE: no @include('partials.public.navbar') here — this microsite
         is intentionally self-contained, per spec: "should NOT contain
         the main ARPS navbar." --}}

    @include('partials.journal-site.header', ['journal' => $journal])

    <main class="bg-body-tertiary min-vh-100">
        @yield('content')
    </main>

    <footer class="bg-dark text-white-50 py-3 mt-5">
        <div class="container small">
            &copy; {{ date('Y') }} {{ $journal['name'] ?? 'Journal' }}. Published under ARPS.
            <a href="{{ route('home') }}" class="link-light ms-2">← Back to ARPS</a>
        </div>
    </footer>

    <script src="{{ asset('vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
