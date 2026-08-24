@extends('layouts.app')
@section('title', 'Events')
@section('meta_description', 'Agenda ARPS — webinar, seminar, kuliah umum, dan konferensi terbaru.')
@section('content')
<div class="container my-5">
    <h1 class="mb-2">Events</h1>
    <p class="text-body-secondary mb-4">
        Informasi kegiatan ARPS — webinar, kuliah umum, kunjungan industri, seminar, dan konferensi.
        Pendaftaran dilakukan manual melalui kontak yang tercantum pada masing-masing kegiatan.
    </p>

    <div class="row g-4">
        @forelse ($events as $event)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('events.show', $event) }}" class="text-decoration-none text-body">
                    <div class="card h-100">
                        @if($event->poster)
                            <img src="{{ asset('storage/'.$event->poster) }}" class="card-img-top" alt="{{ $event->judul }}" style="height:160px;object-fit:cover;">
                        @else
                            <div class="bg-secondary-subtle d-flex align-items-center justify-content-center" style="min-height: 160px;">
                                <span class="text-body-secondary small">[ poster ]</span>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->judul }}</h5>
                            <p class="card-text small text-body-secondary mb-1">
                                <i class="cil-calendar me-1"></i>{{ $event->tanggal_waktu->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                            </p>
                            @if($event->lokasi)
                                <p class="card-text small text-body-secondary mb-1">
                                    <i class="cil-location-pin me-1"></i>{{ $event->lokasi }}
                                </p>
                            @endif
                            @if($event->deskripsi)
                                <p class="card-text small mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($event->deskripsi), 80) }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12"><p class="text-body-secondary">Belum ada event.</p></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $events->links() }}</div>
</div>
@endsection
