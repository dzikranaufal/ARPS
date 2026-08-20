@extends('layouts.admin')

@section('title', 'Organization Structure')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">Organization Structure</div>
                <div class="card-body">

                    {{-- Flash messages placeholder — backend dev will populate via
                         session('success') / session('error') --}}
                    {{--
                    <div class="alert alert-success">Member added successfully.</div>
                    --}}

                    <div class="d-grid gap-2 d-md-flex mb-4">
                        <a class="btn btn-primary" href="{{ route('admin.structure.create') }}" role="button">
                            Add Member
                        </a>
                    </div>

                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Photo</th>
                                <th scope="col">Name</th>
                                <th scope="col">Position</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- ================================================
                                 STATIC SAMPLE ROWS — for layout/design purposes only.
                                 Backend dev: replace everything inside <tbody> with:

                                 @forelse ($members as $i => $member)
                                     <tr> ... use $member->name, $member->position, etc ... </tr>
                                 @empty
                                     <tr><td colspan="5" class="text-center">No members found.</td></tr>
                                 @endforelse
                            ================================================= --}}

                            <tr>
                                <th scope="row">1</th>
                                <td><img src="{{ asset('assets/sample/avatar1.jpg') }}" alt="John" width="48" height="48" class="rounded-circle object-fit-cover"></td>
                                <td>John Wirawan</td>
                                <td>Chairman</td>
                                <td>
                                    <a href="{{ route('admin.structure.edit', 1) }}" class="btn btn-warning" type="button">
                                        <i class="icon cil-pencil"></i>
                                        Edit
                                    </a>
                                    <button class="btn btn-danger" type="button" data-coreui-toggle="modal" data-coreui-target="#deleteModal1">
                                        <i class="icon cil-trash"></i>
                                        Delete
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">2</th>
                                <td><img src="{{ asset('assets/sample/avatar2.jpg') }}" alt="Amara" width="48" height="48" class="rounded-circle object-fit-cover"></td>
                                <td>Amara Putri</td>
                                <td>Managing Editor</td>
                                <td>
                                    <a href="{{ route('admin.structure.edit', 2) }}" class="btn btn-warning" type="button">
                                        <i class="icon cil-pencil"></i>
                                        Edit
                                    </a>
                                    <button class="btn btn-danger" type="button" data-coreui-toggle="modal" data-coreui-target="#deleteModal2">
                                        <i class="icon cil-trash"></i>
                                        Delete
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">3</th>
                                <td><img src="{{ asset('assets/sample/avatar3.jpg') }}" alt="Rian" width="48" height="48" class="rounded-circle object-fit-cover"></td>
                                <td>Rian Hidayat</td>
                                <td>Secretary</td>
                                <td>
                                    <a href="{{ route('admin.structure.edit', 3) }}" class="btn btn-warning" type="button">
                                        <i class="icon cil-pencil"></i>
                                        Edit
                                    </a>
                                    <button class="btn btn-danger" type="button" data-coreui-toggle="modal" data-coreui-target="#deleteModal3">
                                        <i class="icon cil-trash"></i>
                                        Delete
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    {{-- Delete confirmation modals — same pattern as Journals (Step 8) --}}
    @foreach ([1, 2, 3] as $sampleId)
        <div class="modal fade" id="deleteModal{{ $sampleId }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Member</h5>
                        <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to remove this member from the organization structure?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                        {{-- Real delete form (backend dev will wire action URL + @csrf + @method('DELETE')) --}}
                        <button type="button" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection