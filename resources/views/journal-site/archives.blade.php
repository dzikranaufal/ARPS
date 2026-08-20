@php
$journal = ['slug' => $slug, 'name' => 'Innovation in Engineering', 'issn' => '3047-5473'];
@endphp

@extends('layouts.journal-site', ['journal' => $journal])

@section('title', 'Archives — ' . $journal['name'])

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Archives</h2>
    <div class="accordion" id="archivesFull">
        @foreach (['2026', '2025', '2024'] as $i => $year)
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button"
                    data-coreui-toggle="collapse" data-coreui-target="#full{{ $year }}">
                    {{ $year }}
                </button>
            </h2>
            <div id="full{{ $year }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                data-coreui-parent="#archivesFull">
                <div class="accordion-body small text-body-secondary">
                    Issues published in {{ $year }} will be listed here.
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
