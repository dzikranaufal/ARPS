@extends('layouts.admin')

@section('title', 'Journals')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Journals</h2>
    <a href="{{ route('admin.journals.create') }}" class="btn btn-primary">
        <i class="cil-plus me-1"></i> Add Journal
    </a>
</div>

{{-- Flash messages placeholder — backend dev will populate these
         via session('success') / session('error') once forms actually submit --}}
{{--
    <div class="alert alert-success">Journal created successfully.</div>
    --}}

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Publication Date</th>
                        <th>DOI / Link</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- ================================================
                             STATIC SAMPLE ROWS — for layout/design purposes only.
                             Backend dev: replace everything inside <tbody> with:

                             @forelse ($journals as $i => $journal)
                                 <tr> ... use $journal->title, $journal->author, etc ... </tr>
                             @empty
                                 <tr><td colspan="8" class="text-center">No journals found.</td></tr>
                             @endforelse
                        ================================================= --}}

                    <tr>
                        <td>1</td>
                        <td><img src="{{ asset('assets/sample/cover1.jpg') }}" alt="Cover" width="48" height="64"
                                class="rounded object-fit-cover"></td>
                        <td>The Impact of Renewable Energy Policy on Rural Development</td>
                        <td>Dr. Ayu Lestari</td>
                        <td>12 Jan 2026</td>
                        <td><a href="#" target="_blank" class="btn btn-sm btn-outline-primary">10.1000/example.doi.001</a></td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.journals.edit', 1) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="cil-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal"
                                data-coreui-target="#deleteModal1">
                                <i class="cil-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td><img src="{{ asset('assets/sample/cover2.jpg') }}" alt="Cover" width="48" height="64"
                                class="rounded object-fit-cover"></td>
                        <td>Machine Learning Approaches in Early Disease Detection</td>
                        <td>Prof. Bima Santoso</td>
                        <td>03 Feb 2026</td>
                        <td><a href="#" target="_blank" class="btn btn-sm btn-outline-primary">https://doi.org/10.1000/example.002</a></td>
                        <td><span class="badge bg-success">Published</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.journals.edit', 2) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="cil-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal"
                                data-coreui-target="#deleteModal2">
                                <i class="cil-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td><img src="{{ asset('assets/sample/cover3.jpg') }}" alt="Cover" width="48" height="64"
                                class="rounded object-fit-cover"></td>
                        <td>Community-Based Approaches to Coastal Waste Management</td>
                        <td>Siti Rahmawati, M.Sc.</td>
                        <td>28 Nov 2025</td>
                        <td><a href="#" target="_blank" class="btn btn-sm btn-outline-primary">10.1000/example.doi.003</a></td>
                        <td><span class="badge bg-secondary">Archived</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.journals.edit', 3) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="cil-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal"
                                data-coreui-target="#deleteModal3">
                                <i class="cil-trash"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Delete confirmation modals — one per row for now (static demo).
        Backend dev will likely make this a single reusable modal that
        gets its target ID set via JS when a delete button is clicked,
        rather than one modal per row once data is dynamic. --}}

@foreach ([1, 2, 3] as $sampleId)
<div class="modal fade" id="deleteModal{{ $sampleId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Journal</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this journal? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                {{-- Real delete form (backend dev will wire the action URL + @csrf + @method('DELETE')) --}}
                <button type="button" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
