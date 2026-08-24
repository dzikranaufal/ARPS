@extends('layouts.app')
@section('title', $publication->judul)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($publication->deskripsi ?? $publication->judul), 150))
@section('content')
<div class="container my-5">
    <a href="{{ route('publications.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Kembali</a>
    <div class="row">
        <div class="col-lg-8">
            <span class="badge bg-primary mb-2">{{ $publication->kategori->value }}</span>
            <h1 class="mb-2">{{ $publication->judul }}</h1>
            <p class="small text-body-secondary mb-3">Oleh {{ $publication->member->nama ?? '—' }} • {{ $publication->created_at->format('d M Y') }}</p>
            @if($publication->deskripsi)
                <div class="prose mb-4">{{ $publication->deskripsi }}</div>
            @endif
            @if($publication->file)
                <a href="{{ \Illuminate\Support\Facades\Storage::url($publication->file) }}" target="_blank" class="btn btn-primary">Unduh File</a>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6>Kategori</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['tulisan','prestasi','produk','pkm'] as $cat)
                            <a href="{{ route('publications.index',['kategori'=>$cat]) }}" class="btn btn-sm {{ $publication->kategori->value===$cat?'btn-primary':'btn-outline-secondary' }}">{{ ucfirst($cat) }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
