@extends('layouts.app')
@section('title', $program->judul)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($program->deskripsi ?? ''), 150))
@section('content')
<div class="container my-5">
    <a href="{{ route('programs.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Kembali ke Programs</a>
    <div class="row">
        <div class="col-lg-8">
            @if($program->gambar)
                <img src="{{ asset('storage/'.$program->gambar) }}" alt="{{ $program->judul }}" class="img-fluid rounded mb-4" style="max-height:400px;object-fit:cover;width:100%;">
            @endif
            <div class="mb-2">
                @if($program->kategori)<span class="badge bg-primary">{{ $program->kategori->nama }}</span>@endif
            </div>
            <h1 class="mb-3">{{ $program->judul }}</h1>
            @if($program->deskripsi)
                <div class="prose">{!! \Mews\Purifier\Facades\Purifier::clean($program->deskripsi) !!}</div>
            @else
                <p class="text-body-secondary">Belum ada deskripsi.</p>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header fw-bold">Informasi</div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item"><strong>Kategori:</strong> {{ $program->kategori->nama ?? '—' }}</div>
                    <a href="{{ route('programs.index') }}" class="list-group-item list-group-item-action">Semua Programs</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
