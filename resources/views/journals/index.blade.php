@extends('layouts.app')

@section('title', 'Journals')

@section('content')

<div class="container my-5">

    <div class="mb-4">
        <h1>Journals</h1>
        <p class="text-body-secondary">Browse journals published under the ARPS network.</p>
    </div>

    {{-- ================================================
             STATIC SAMPLE JOURNALS — for layout purposes only.
             Backend dev: replace with @foreach ($journals as $journal) ...
             Each card should link to journal.home using $journal->slug.
        ================================================= --}}

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">

        <div class="col">
            <a href="{{ route('journal.home', ['slug' => 'ai']) }}" class="text-decoration-none text-body">
                <div class="card h-100 shadow-sm journal-card">
                    <div class="bg-dark d-flex align-items-center justify-content-center" style="aspect-ratio: 3/4;">
                        <span class="text-white-50 small text-center px-2">[ Innovation in Engineering cover ]</span>
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 small fw-bold">Innovation in Engineering</h6>
                        <p class="card-text text-body-secondary small mb-0">E-ISSN 3047-5473</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="{{ route('journal.home', ['slug' => 'cs']) }}" class="text-decoration-none text-body">
                <div class="card h-100 shadow-sm journal-card">
                    <div class="bg-secondary d-flex align-items-center justify-content-center"
                        style="aspect-ratio: 3/4;">
                        <span class="text-white-50 small text-center px-2">[ Computer Science cover ]</span>
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 small fw-bold">Computer Science &amp; Applications</h6>
                        <p class="card-text text-body-secondary small mb-0">E-ISSN 3047-5481</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="{{ route('journal.home', ['slug' => 'cvi']) }}" class="text-decoration-none text-body">
                <div class="card h-100 shadow-sm journal-card">
                    <div class="bg-dark-subtle d-flex align-items-center justify-content-center"
                        style="aspect-ratio: 3/4;">
                        <span class="text-body-secondary small text-center px-2">[ Cybersecurity cover ]</span>
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 small fw-bold">Cybersecurity &amp; Vulnerability</h6>
                        <p class="card-text text-body-secondary small mb-0">E-ISSN 3047-5499</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="{{ route('journal.home', ['slug' => 'edu']) }}" class="text-decoration-none text-body">
                <div class="card h-100 shadow-sm journal-card">
                    <div class="bg-secondary d-flex align-items-center justify-content-center"
                        style="aspect-ratio: 3/4;">
                        <span class="text-white-50 small text-center px-2">[ Sustainable Education cover ]</span>
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title mb-1 small fw-bold">AI for Sustainable Education</h6>
                        <p class="card-text text-body-secondary small mb-0">E-ISSN 3047-5502</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

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
