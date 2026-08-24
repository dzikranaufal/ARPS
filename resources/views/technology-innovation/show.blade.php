@extends('layouts.app')
@section('title', $innovation->judul)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($innovation->deskripsi ?? ''), 150))
@section('content')
<div class="container my-5">
    <a href="{{ route('technology-innovation.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Kembali</a>
    <div class="row">
        <div class="col-lg-8">
            @if($innovation->gambar)
                <img src="{{ asset('storage/'.$innovation->gambar) }}" alt="{{ $innovation->judul }}" class="img-fluid rounded mb-4" style="max-height:400px;object-fit:cover;width:100%;">
            @endif
            <span class="badge {{ $innovation->status->value==='aktif'?'bg-success':'bg-secondary' }} mb-2">{{ $innovation->status->value }}</span>
            <h1 class="mb-3">{{ $innovation->judul }}</h1>
            @if($innovation->deskripsi)
                <div class="prose">{!! \Mews\Purifier\Facades\Purifier::clean($innovation->deskripsi) !!}</div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('technology-innovation.index') }}" class="btn btn-outline-primary w-100">Semua Inovasi</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
