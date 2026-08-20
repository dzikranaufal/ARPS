{{-- resources/views/partials/journal-site/header.blade.php --}}
<div class="bg-dark text-white py-4">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                style="width:56px;height:56px;">
                <i class="cil-globe-alt fs-3"></i>
            </div>
            <h1 class="h3 mb-0">{{ $journal['name'] ?? 'Journal' }}</h1>
        </div>
        <div class="text-end">
            <div class="fw-bold">{{ $journal['issn'] ?? '—' }}</div>
            <div class="small text-white-50">E-ISSN</div>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <button class="navbar-toggler" type="button" data-coreui-toggle="collapse" data-coreui-target="#journalNav"
            aria-controls="journalNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="journalNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('journal.home') ? 'active fw-bold' : '' }}"
                        href="{{ route('journal.home', ['slug' => $journal['slug']]) }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Journal Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('journal.archives') ? 'active fw-bold' : '' }}"
                        href="{{ route('journal.archives', ['slug' => $journal['slug']]) }}">Archives</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('journal.guidelines') ? 'active fw-bold' : '' }}"
                        href="{{ route('journal.guidelines', ['slug' => $journal['slug']]) }}">Author Guidelines</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Submit Manuscript</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Login</a>
                </li>
            </ul>
            <form class="d-flex ms-auto" role="search">
                <input class="form-control form-control-sm" type="search" placeholder="Search">
            </form>
        </div>
    </div>
</nav>
