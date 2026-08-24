{{-- resources/views/partials/public/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">ARPS</a>

        <button class="navbar-toggler" type="button" data-coreui-toggle="collapse" data-coreui-target="#publicNav"
            aria-controls="publicNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('programs.*') ? 'active' : '' }}"
                        href="{{ route('programs.index') }}">Programs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('technology-innovation.*') ? 'active' : '' }}"
                        href="{{ route('technology-innovation.index') }}">Tech Innovation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('journals.*') ? 'active' : '' }}"
                        href="{{ route('journals.index') }}">Journal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('publications.*') ? 'active' : '' }}"
                        href="{{ route('publications.index') }}">Publication</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('news.*') ? 'active' : '' }}"
                        href="{{ route('news.index') }}">News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}"
                        href="{{ route('events.index') }}">Event</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('organization.*') ? 'active' : '' }}"
                        href="{{ route('organization.index') }}">Organization</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                        href="{{ route('about') }}">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact.index') ? 'active' : '' }}"
                        href="{{ route('contact.index') }}">Contact</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-outline-light btn-sm" href="{{ route('register') }}">Register</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
