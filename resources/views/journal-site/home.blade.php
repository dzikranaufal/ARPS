@php
// ================================================
// STATIC SAMPLE DATA — for layout purposes only.
// Backend dev: this will come from a Journal model lookup by slug,
// e.g. Journal::where('slug', $slug)->firstOrFail()
// ================================================
$journal = [
'slug' => $slug,
'name' => 'Innovation in Engineering',
'issn' => '3047-5473',
'description' => 'Innovation in Engineering is an international peer-reviewed journal that publishes research focusing
on applied engineering, computational analysis, and practical problem-solving in engineering systems. The journal
provides a platform for researchers, engineers, and practitioners to share studies that emphasize the application of
engineering principles in real-world contexts.',
'badges' => ['Open Access', 'Peer-Reviewed'],
];

@endphp

@extends('layouts.journal-site', ['journal' => $journal])

@section('title', $journal['name'])

@section('content')

<div class="container py-5">

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="bg-dark rounded d-flex align-items-center justify-content-center"
                        style="aspect-ratio: 3/4;">
                        <span class="text-white-50 small text-center px-2">[ {{ $journal['name'] }} cover ]</span>
                    </div>
                </div>
                <div class="col-md-9">
                    <p>{!! nl2br(e($journal['description'])) !!}</p>
                    <a href="#" class="fw-bold small text-decoration-none">View full aims &amp; scope →</a>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        @foreach ($journal['badges'] as $badge)
                        <span class="badge rounded-pill text-bg-light border">{{ $badge }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">Current Issue</div>
        <div class="card-body">
            <h6 class="fw-bold">Vol. 3 No. 1 (2026): Regular Issue</h6>
            <p class="text-body-secondary small mb-3">
                This issue comprises articles authored or co-authored by scholars representing
                multiple institutions across several countries.
            </p>

            {{-- ================================================
                     STATIC SAMPLE ARTICLES — for layout purposes only.
                ================================================= --}}
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <a href="#" class="text-decoration-none fw-semibold">
                        Optimization of Structural Load Distribution Using Finite Element Analysis
                    </a>
                    <div class="text-body-secondary small">Ahmad Fauzan, Siti Rahma</div>
                </li>
                <li class="list-group-item">
                    <a href="#" class="text-decoration-none fw-semibold">
                        A Comparative Study of Renewable Energy Integration Models
                    </a>
                    <div class="text-body-secondary small">Novan Arrizal</div>
                </li>
            </ul>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">Archives</div>
        <div class="card-body">
            <div class="accordion" id="archivesAccordion">
                @foreach (['2026', '2025', '2024'] as $i => $year)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button"
                            data-coreui-toggle="collapse" data-coreui-target="#year{{ $year }}">
                            {{ $year }}
                        </button>
                    </h2>
                    <div id="year{{ $year }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                        data-coreui-parent="#archivesAccordion">
                        <div class="accordion-body small text-body-secondary">
                            Issues published in {{ $year }} will be listed here.
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection
