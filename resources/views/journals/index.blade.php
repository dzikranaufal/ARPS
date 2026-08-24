@extends('layouts.app')

@section('title', 'Journals')

@section('meta_description', 'Katalog jurnal ARPS — referensi eksternal dan jaringan publikasi mitra.')
@section('content')

<div class="container my-5">

    <div class="mb-4">
        <h1>Journals</h1>
        <p class="text-body-secondary">Browse journals published under the ARPS network.</p>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">

        @forelse ($journals as $journal)
            <div class="col">
                <a href="{{ $journal->link_eksternal }}" target="_blank" rel="noopener"
                    class="text-decoration-none text-body">
                    <div class="card h-100 shadow-sm journal-card">
                        @if($journal->cover)
                            <img src="{{ asset('storage/'.$journal->cover) }}" alt="{{ $journal->nama }}" class="card-img-top" style="aspect-ratio: 3/4; object-fit: cover;">
                        @else
                            <div class="bg-dark d-flex align-items-center justify-content-center" style="aspect-ratio: 3/4;">
                                <span class="text-white-50 small text-center px-2">[ cover ]</span>
                            </div>
                        @endif
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1 small fw-bold">{{ $journal->nama }}</h6>
                            <p class="card-text text-body-secondary small mb-0">E-ISSN {{ $journal->e_issn }}</p>
                            @if ($journal->deskripsi)
                                <p class="card-text text-body-secondary small mb-0 mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($journal->deskripsi),80) }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <p class="text-body-secondary">Belum ada jurnal.</p>
            </div>
        @endforelse

    </div>

    @if(method_exists($journals, 'links'))
        <div class="mt-4">{{ $journals->links() }}</div>
    @endif

</div>

@endsection

@push('styles')
<style>
    .journal-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .journal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

</style>
@endpush
