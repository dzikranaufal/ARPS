@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'ARPS — wadah kolaborasi akademisi, peneliti, dan praktisi. Temukan program, jurnal, dan kegiatan terbaru.')

@section('content')
<h1 class="visually-hidden">ARPS — Academics, Researchers, and Practitioners Society</h1>

<div id="heroCarousel" class="carousel slide" data-coreui-ride="carousel">
    <div class="carousel-indicators">
        @forelse($heroes as $idx => $hero)
            <button type="button" data-coreui-target="#heroCarousel" data-coreui-slide-to="{{ $idx }}" class="{{ $idx===0?'active':'' }}" @if($idx===0) aria-current="true" @endif aria-label="Slide {{ $idx+1 }}"></button>
        @empty
            <button type="button" data-coreui-target="#heroCarousel" data-coreui-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-coreui-target="#heroCarousel" data-coreui-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-coreui-target="#heroCarousel" data-coreui-slide-to="2" aria-label="Slide 3"></button>
        @endforelse
    </div>
    <div class="carousel-inner">
        @forelse($heroes as $idx => $hero)
            <div class="carousel-item {{ $idx===0?'active':'' }}">
                @if($hero->gambar)
                    <img src="{{ asset('storage/'.$hero->gambar) }}" class="d-block w-100" style="height:420px;object-fit:cover;" alt="{{ $hero->judul }}">
                @else
                    <div class="bg-dark d-flex align-items-center justify-content-center" style="height: 420px;"><span class="text-white-50">[ Hero image ]</span></div>
                @endif
                <div class="carousel-caption d-none d-md-block text-start bg-dark bg-opacity-50 rounded p-2">
                    <h5>{{ $hero->judul }}</h5>
                    @if($hero->deskripsi)<p>{!! \Illuminate\Support\Str::limit(strip_tags($hero->deskripsi), 120) !!}</p>@endif
                    @if($hero->link)<a href="{{ $hero->link }}" target="_blank" class="btn btn-sm btn-light">Learn More</a>@endif
                </div>
            </div>
        @empty
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
        @endforelse
    </div>
    <button class="carousel-control-prev" type="button" data-coreui-target="#heroCarousel" data-coreui-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-coreui-target="#heroCarousel" data-coreui-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>
    </button>
</div>

<div class="container my-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="mb-4">Latest News</h2>
            @forelse($latestNews as $item)
                <div class="card mb-4">
                    <div class="row g-0">
                        <div class="col-md-4">
                            @if($item->gambar)
                                <img src="{{ asset('storage/'.$item->gambar) }}" class="img-fluid h-100" style="object-fit:cover;min-height:160px;" alt="{{ $item->judul }}">
                            @else
                                <div class="bg-secondary-subtle h-100 d-flex align-items-center justify-content-center" style="min-height: 160px;"><span class="text-body-secondary small">[ image ]</span></div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title"><a href="{{ route('news.show',$item) }}" class="text-decoration-none text-body">{{ $item->judul }}</a></h5>
                                <p class="card-text text-body-secondary small mb-1">{{ $item->tanggal_publish->timezone('Asia/Jakarta')->format('d M Y') }}</p>
                                <p class="card-text">{{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 120) }}</p>
                                <a href="{{ route('news.show',$item) }}" class="btn btn-sm btn-outline-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-body-secondary">Belum ada berita.</p>
            @endforelse
            <a href="{{ route('news.index') }}" class="btn btn-outline-primary">Semua News</a>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header fw-bold">About ARPS</div>
                <div class="card-body">
                    <div class="small mb-0">
                        @if($profile && $profile->deskripsi)
                            {!! \Mews\Purifier\Facades\Purifier::clean($profile->deskripsi) !!}
                        @else
                            ARPS (Academics, Researchers, and Practitioners Society) is a community connecting academics, researchers, and practitioners across engineering, social sciences, and applied research, nationally and internationally.
                        @endif
                    </div>
                    <a href="{{ route('about') }}" class="btn btn-sm btn-outline-primary mt-2">Selengkapnya</a>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header fw-bold">Quick Links</div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('journals.index') }}" class="list-group-item list-group-item-action">Journals</a>
                    <a href="{{ route('events.index') }}" class="list-group-item list-group-item-action">Upcoming Events</a>
                    <a href="{{ route('register') }}" class="list-group-item list-group-item-action">Register</a>
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
