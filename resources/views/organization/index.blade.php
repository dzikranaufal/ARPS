@extends('layouts.app')
@section('title', 'Organization')
@section('meta_description', 'Struktur pengurus dan direktori anggota ARPS — transparansi organisasi dan jejaring member.')
@section('content')
<div class="container my-5">

    <h1 class="mb-2">Organization</h1>
    <p class="text-body-secondary mb-4">Struktur pengurus dan direktori anggota ARPS.</p>

    @if($profile)
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ $profile->nama }}</h5>
                @if($profile->deskripsi)<div class="card-text">{!! \Mews\Purifier\Facades\Purifier::clean($profile->deskripsi) !!}</div>@endif
                @if($profile->visi)<div class="mt-2"><strong>Visi:</strong><div class="small">{!! \Mews\Purifier\Facades\Purifier::clean($profile->visi) !!}</div></div>@endif
                @if($profile->misi)<div class="mt-2"><strong>Misi:</strong><div class="small">{!! \Mews\Purifier\Facades\Purifier::clean($profile->misi) !!}</div></div>@endif
            </div>
        </div>
    @endif

    <h2 class="mb-3">Struktur Pengurus</h2>
    <div class="row g-4 mb-3">
        @forelse ($structures as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        @if($item->foto)
                            <img src="{{ asset('storage/'.$item->foto) }}" alt="{{ $item->nama_pengurus }}" width="96" height="96" class="rounded-circle object-fit-cover border mb-3">
                        @else
                            <div class="bg-secondary-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 96px; height: 96px;">
                                <span class="text-body-secondary small">[ foto ]</span>
                            </div>
                        @endif
                        <h5 class="card-title mb-1">{{ $item->nama_pengurus }}</h5>
                        <p class="text-primary small fw-bold mb-1">{{ $item->jabatan }}</p>
                        @if($item->afiliasi)<p class="card-text small text-body-secondary mb-0">{{ $item->afiliasi }}</p>@endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-body-secondary">Belum ada data pengurus.</p></div>
        @endforelse
    </div>
    <div class="mb-5">{{ $structures->links() }}</div>

    <h2 class="mb-3">Direktori Member</h2>
    <div class="row g-4">
        @forelse ($members as $m)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        @if($m->foto)
                            <img src="{{ asset('storage/'.$m->foto) }}" alt="{{ $m->nama }}" width="80" height="80" class="rounded-circle object-fit-cover border mb-3">
                        @else
                            <div class="bg-secondary-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;"><span class="text-body-secondary small">[ foto ]</span></div>
                        @endif
                        <h6 class="card-title mb-1">{{ $m->nama }}</h6>
                        @if($m->organisasi)<p class="card-text small text-body-secondary mb-0">{{ $m->organisasi }}</p>@else<p class="card-text small text-body-secondary mb-0">—</p>@endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-body-secondary">Belum ada direktori member.</p></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $members->links() }}</div>

</div>
@endsection
