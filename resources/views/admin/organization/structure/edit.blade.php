@extends('layouts.admin')

@section('title', 'Edit Member')

@section('content')

{{-- ================================================
         STATIC SAMPLE DATA — for layout/design purposes only.
         Backend dev: replace hardcoded values with $member->name,
         $member->position, etc. via route model binding.
    ================================================= --}}

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">Edit Member</div>
            <div class="card-body">

                {{-- Real form (backend dev will add
                         action="{{ route('admin.structure.update', 1) }}" method="POST",
                @csrf, @method('PUT'), enctype="multipart/form-data" --}}
                <form class="row g-3 needs-validation" id="memberEditForm" novalidate>

                    <div class="col-12">
                        <label class="form-label" for="memberName">Name</label>
                        <input class="form-control" id="memberName" name="name" type="text" value="John Wirawan"
                            required>
                        <div class="invalid-feedback">Please enter a name.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="memberPosition">Position</label>
                        <input class="form-control" id="memberPosition" name="position" type="text" value="Chairman"
                            required>
                        <div class="invalid-feedback">Please enter a position.</div>
                    </div>

                    {{-- Photo: current photo shown, replacing it is optional --}}
                    <div class="col-12">
                        <label class="form-label d-block">Profile Photo</label>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <img src="{{ asset('assets/sample/avatar1.jpg') }}" alt="Current photo" width="56"
                                height="56" class="rounded-circle object-fit-cover border">
                            <span class="text-body-secondary small">Current photo</span>
                        </div>
                        <label class="form-label" for="memberPhoto">Replace photo (optional)</label>
                        <input class="form-control" id="memberPhoto" name="photo" type="file" accept=".png,.jpg,.jpeg"
                            aria-label="PNG or JPG">
                        <div class="invalid-feedback">Please upload a valid image (PNG or JPG).</div>
                        <div class="form-text">Leave empty to keep the current photo.</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                        <button class="btn btn-warning" type="submit" id="saveBtn">Save Changes</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

{{-- Save confirmation modal --}}
<div class="modal fade" id="confirmSaveModal" data-coreui-backdrop="static" data-coreui-keyboard="false" tabindex="-1"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Changes</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to save all the changes you've made?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Keep Editing</button>
                <button type="button" class="btn btn-success" id="confirmSaveBtn">Save Changes</button>
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

        const form = document.getElementById('memberEditForm');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmSaveBtn = document.getElementById('confirmSaveBtn');

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
                const modal = coreui.Modal.getOrCreateInstance(document.getElementById('confirmSaveModal'));
                modal.show();
            }

            form.classList.add('was-validated');
        }, false);

        confirmSaveBtn.addEventListener('click', function () {
            const modal = coreui.Modal.getInstance(document.getElementById('confirmSaveModal'));
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
