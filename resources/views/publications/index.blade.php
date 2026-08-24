@extends('layouts.app')
@section('title', 'Publications')
@section('meta_description', 'Karya member ARPS — tulisan, prestasi, produk teknologi, dan PkM yang disetujui.')
@section('content')
<div class="container my-5">
    <h1 class="mb-2">Publications</h1>
    <p class="text-body-secondary mb-4">Karya anggota ARPS — tulisan, prestasi, produk teknologi, dan karya PkM yang telah disetujui.</p>

    @php
        $cats = ['tulisan'=>'Tulisan','prestasi'=>'Prestasi','produk'=>'Produk','pkm'=>'PkM'];
        $active = request('kategori');
    @endphp
    <div class="mb-4 d-flex flex-wrap gap-2">
        <a href="{{ route('publications.index') }}" class="btn btn-sm {{ !$active ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
        @foreach($cats as $key => $label)
            <a href="{{ route('publications.index', ['kategori'=>$key]) }}" class="btn btn-sm {{ $active===$key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="row g-4">
        @forelse ($publications as $pub)
            <div class="col-md-6 col-lg-3">
                <a href="{{ route('publications.show', $pub) }}" class="text-decoration-none text-body">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-primary align-self-start mb-2">{{ $pub->kategori->value }}</span>
                            <h5 class="card-title">{{ $pub->judul }}</h5>
                            <p class="card-text small text-body-secondary mb-1">{{ $pub->member->nama ?? '—' }}</p>
                            @if($pub->deskripsi)<p class="card-text small mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($pub->deskripsi), 80) }}</p>@endif
                            <span class="btn btn-sm btn-outline-primary mt-auto">Lihat Detail</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12"><p class="text-body-secondary">Belum ada publikasi.</p></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $publications->links() }}</div>
</div>
@endsection
