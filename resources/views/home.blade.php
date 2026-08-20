@extends('layouts.app')

@section('title', 'Home')

@section('content')

{{-- Hero carousel — dummy slides, data-coreui-* attributes (not data-bs-*)
         since CoreUI's JS bundle uses its own namespaced attributes --}}
<div id="heroCarousel" class="carousel slide" data-coreui-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-coreui-target="#heroCarousel" data-coreui-slide-to="0" class="active"
            aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-coreui-target="#heroCarousel" data-coreui-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-coreui-target="#heroCarousel" data-coreui-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="bg-dark d-flex align-items-center justify-content-center" style="height: 420px;">
                <span class="text-white-50">[ Hero image placeholder 1 ]</span>
            </div>
            <div class="carousel-caption d-none d-md-block text-start">
                <h5>International Conference 2026</h5>
                <p>Join ARPS members at the upcoming conference in Ankara, Turkey.</p>
            </div>
        </div>
        <div class="carousel-item">
            <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 420px;">
                <span class="text-white-50">[ Hero image placeholder 2 ]</span>
            </div>
            <div class="carousel-caption d-none d-md-block text-start">
                <h5>Latest Journal Publications</h5>
                <p>Explore research from our academic community.</p>
            </div>
        </div>
        <div class="carousel-item">
            <div class="bg-dark-subtle d-flex align-items-center justify-content-center" style="height: 420px;">
                <span class="text-body-secondary">[ Hero image placeholder 3 ]</span>
            </div>
            <div class="carousel-caption d-none d-md-block text-start">
                <h5>Become a Member</h5>
                <p>Free membership open to academics, researchers, and practitioners.</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-coreui-target="#heroCarousel" data-coreui-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-coreui-target="#heroCarousel" data-coreui-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<div class="container my-5">
    <div class="row g-4">

        {{-- Main content column --}}
        <div class="col-lg-8">
            <h2 class="mb-4">Latest News</h2>

            {{-- ================================================
                     STATIC SAMPLE CONTENT — for layout purposes only.
                     Backend dev: replace with @foreach ($news as $item) ...
                ================================================= --}}
            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-md-4">
                        <div class="bg-secondary-subtle h-100 d-flex align-items-center justify-content-center"
                            style="min-height: 160px;">
                            <span class="text-body-secondary small">[ image ]</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">ARPS Delegation Visits Research Partner Institution</h5>
                            <p class="card-text text-body-secondary small mb-1">August 15, 2026</p>
                            <p class="card-text">
                                ARPS representatives met with partner institutions to discuss
                                upcoming collaborative research initiatives and joint publications.
                            </p>
                            <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-primary">Read More</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-md-4">
                        <div class="bg-secondary-subtle h-100 d-flex align-items-center justify-content-center"
                            style="min-height: 160px;">
                            <span class="text-body-secondary small">[ image ]</span>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">Call for Papers: Upcoming Journal Issue</h5>
                            <p class="card-text text-body-secondary small mb-1">August 10, 2026</p>
                            <p class="card-text">
                                ARPS is now accepting submissions for the next issue covering
                                engineering, social sciences, and applied research topics.
                            </p>
                            <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-primary">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar column --}}
        <div class="col-lg-4">

            <div class="card mb-4">
                <div class="card-header fw-bold">About ARPS</div>
                <div class="card-body">
                    <p class="small mb-0">
                        ARPS (Academics, Researchers, and Practitioners Society) is a community
                        connecting academics, researchers, and practitioners across engineering,
                        social sciences, and applied research, nationally and internationally.
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-bold">Quick Links</div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('journals.index') }}" class="list-group-item list-group-item-action">Journals</a>
                    <a href="{{ route('events.index') }}" class="list-group-item list-group-item-action">Upcoming
                        Events</a>
                    <a href="{{ route('membership.index') }}" class="list-group-item list-group-item-action">Become a
                        Member</a>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-bold">Social Media</div>
                <div class="card-body d-flex gap-2">
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="cil-envelope-closed"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="cil-globe-alt"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="cil-share"></i></a>
                </div>
            </div>

        </div>

    </div>
</div>

@endsection
