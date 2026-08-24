@extends('layouts.app')
@section('title', $event->judul)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($event->deskripsi ?? $event->judul), 150))
@section('content')
<div class="container my-5">
    <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Kembali ke Events</a>
    <div class="row">
        <div class="col-lg-8">
            @if($event->poster)
                <img src="{{ asset('storage/'.$event->poster) }}" alt="{{ $event->judul }}" class="img-fluid rounded mb-4" style="max-height:400px;object-fit:cover;width:100%;">
            @endif
            <h1 class="mb-3">{{ $event->judul }}</h1>
            <p class="small text-body-secondary mb-1"><i class="cil-calendar me-1"></i>{{ $event->tanggal_waktu->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
            @if($event->lokasi)<p class="small text-body-secondary mb-3"><i class="cil-location-pin me-1"></i>{{ $event->lokasi }}</p>@endif
            @if($event->deskripsi)<div class="prose mb-3">{!! \Mews\Purifier\Facades\Purifier::clean($event->deskripsi) !!}</div>@endif
            @if($event->info_kontak_pendaftaran)
                <div class="alert alert-info">
                    <strong>Kontak pendaftaran:</strong>
                    @if(\Illuminate\Support\Str::startsWith($event->info_kontak_pendaftaran, ['http://','https://','mailto:']))
                        <a href="{{ $event->info_kontak_pendaftaran }}" target="_blank">{{ $event->info_kontak_pendaftaran }}</a>
                    @else
                        {{ $event->info_kontak_pendaftaran }}
                    @endif
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('events.index') }}" class="btn btn-outline-primary w-100">Semua Events</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
