@extends('layouts.admin')

@section('title', 'Add Member')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">Add Member</div>
            <div class="card-body">

                {{-- Real form (backend dev will add
                        action="{{ route('admin.structure.store') }}" method="POST",
                @csrf, enctype="multipart/form-data" --}}
                <form class="row g-3 needs-validation" id="memberCreateForm" novalidate>

                    <div class="col-12">
                        <label class="form-label" for="memberName">Name</label>
                        <input class="form-control" id="memberName" name="name" type="text"
                            placeholder="e.g. Mark Zuckerberg" required>
                        <div class="invalid-feedback">Please enter a name.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="memberPosition">Position</label>
                        <input class="form-control" id="memberPosition" name="position" type="text"
                            placeholder="e.g. Chairman, Managing Editor" required>
                        <div class="invalid-feedback">Please enter a position.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="memberPhoto">Profile Photo</label>
                        <input class="form-control" id="memberPhoto" name="photo" type="file" accept=".png,.jpg,.jpeg"
                            aria-label="PNG or JPG" required>
                        <div class="invalid-feedback">Please upload a profile photo (PNG or JPG).</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                        <button class="btn btn-success" type="submit" id="saveBtn">Add New</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

{{-- Add confirmation modal --}}
<div class="modal fade" id="confirmAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Member</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to add this member to the organization structure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmAddBtn">Add Member</button>
            </div>
        </div>
    </div>
</div>

{{-- Unsaved changes modal --}}
<div class="modal fade" id="unsavedChangesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unsaved Changes</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to leave? Your unsaved changes will be discarded.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Stay on Page</button>
                <a href="{{ route('admin.structure.index') }}" class="btn btn-danger">Discard Changes</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        "use strict";

        const form = document.getElementById('memberCreateForm');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmAddBtn = document.getElementById('confirmAddBtn');

        let formTouched = false;
        form.addEventListener('input', () => {
            formTouched = true;
        });
        form.addEventListener('change', () => {
            formTouched = true;
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (form.checkValidity()) {
                const modal = coreui.Modal.getOrCreateInstance(document.getElementById('confirmAddModal'));
                modal.show();
            }

            form.classList.add('was-validated');
        }, false);

        confirmAddBtn.addEventListener('click', function () {
            const modal = coreui.Modal.getInstance(document.getElementById('confirmAddModal'));
            modal.hide();
            form.submit();
        });

        cancelBtn.addEventListener('click', function () {
            if (formTouched) {
                const modal = coreui.Modal.getOrCreateInstance(document.getElementById(
                    'unsavedChangesModal'));
                modal.show();
            } else {
                window.location.href = "{{ route('admin.structure.index') }}";
            }
        });

    })();

</script>
@endpush
