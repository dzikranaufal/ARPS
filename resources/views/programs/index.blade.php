@extends('layouts.app')
@section('title', 'Programs')
@section('meta_description', 'Program ARPS — akademik, penelitian, praktik, engineering, sosial, dan inovasi teknologi.')
@section('content')
<div class="container my-5">
    <h1 class="mb-2">Programs</h1>
    <p class="text-body-secondary mb-4">
        Program-program ARPS di berbagai bidang — akademik, penelitian, praktik,
        engineering, sosial, dan inovasi teknologi.
    </p>

    <div class="row g-4">
        @forelse ($programs as $program)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('programs.show', $program) }}" class="text-decoration-none text-body">
                    <div class="card h-100">
                        @if($program->gambar)
                            <img src="{{ asset('storage/'.$program->gambar) }}" class="card-img-top" alt="{{ $program->judul }}" style="height:140px;object-fit:cover;">
                        @else
                            <div class="bg-secondary-subtle d-flex align-items-center justify-content-center" style="min-height: 140px;">
                                <span class="text-body-secondary small">[ image ]</span>
                            </div>
                        @endif
                        <div class="card-body">
                            @if($program->kategori)
                                <span class="badge bg-primary mb-2">{{ $program->kategori->nama }}</span>
                            @else
                                <span class="badge bg-secondary mb-2">Tanpa kategori</span>
                            @endif
                            <h5 class="card-title">{{ $program->judul }}</h5>
                            @if($program->deskripsi)
                                <p class="card-text small mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($program->deskripsi), 100) }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12"><p class="text-body-secondary">Belum ada program.</p></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $programs->links() }}</div>
</div>
@endsection
