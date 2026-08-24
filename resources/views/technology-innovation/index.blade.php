@extends('layouts.app')
@section('title', 'Technology Innovation')
@section('meta_description', 'Inovasi teknologi ARPS — kolaborasi riset dan pengembangan solusi nyata.')
@section('content')
<div class="container my-5">
    <h1 class="mb-2">Technology Innovation</h1>
    <p class="text-body-secondary mb-4">
        Inisiatif inovasi teknologi ARPS — highlight pengembangan dan penerapan
        teknologi untuk menjawab tantangan nyata.
    </p>

    <div class="row g-4">
        @forelse ($innovations as $item)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('technology-innovation.show', $item) }}" class="text-decoration-none text-body">
                    <div class="card h-100">
                        @if($item->gambar)
                            <img src="{{ asset('storage/'.$item->gambar) }}" class="card-img-top" alt="{{ $item->judul }}" style="height:160px;object-fit:cover;">
                        @else
                            <div class="bg-secondary-subtle d-flex align-items-center justify-content-center" style="min-height: 160px;">
                                <span class="text-body-secondary small">[ image ]</span>
                            </div>
                        @endif
                        <div class="card-body">
                            <span class="badge {{ $item->status->value === 'aktif' ? 'bg-success' : 'bg-secondary' }} mb-2">{{ $item->status->value }}</span>
                            <h5 class="card-title">{{ $item->judul }}</h5>
                            @if($item->deskripsi)
                                <p class="card-text small mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($item->deskripsi), 100) }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12"><p class="text-body-secondary">Belum ada inovasi teknologi.</p></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $innovations->links() }}</div>
</div>
@endsection
