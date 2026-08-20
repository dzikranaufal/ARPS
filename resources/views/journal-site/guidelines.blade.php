@php
    $journal = ['slug' => $slug, 'name' => 'Innovation in Engineering', 'issn' => '3047-5473'];
@endphp

@extends('layouts.journal-site', ['journal' => $journal])

@section('title', 'Author Guidelines — ' . $journal['name'])

@section('content')
    <div class="container py-5">
        <h2 class="mb-4">Author Guidelines</h2>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-body-secondary">
                    Manuscript formatting requirements, submission checklist, and review process
                    details will be published here.
                </p>
            </div>
        </div>
    </div>
@endsection