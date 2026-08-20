@extends('layouts.admin')

@section('title', 'Add Journal')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">Add Journal</div>
            <div class="card-body">

                {{-- Real form (backend dev will add action="{{ route('admin.journals.store') }}"
                method="POST", @csrf, enctype="multipart/form-data" for file uploads,
                and @error()/old() directives once validation exists server-side) --}}
                <form class="row g-3 needs-validation" id="journalCreateForm" novalidate>

                    <div class="col-12">
                        <label class="form-label" for="title">Title</label>
                        <input class="form-control" id="title" name="title" type="text"
                            placeholder="e.g. Artificial Intelligence for Sustainable Education" required>
                        <div class="invalid-feedback">Please enter a title.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="author">Author</label>
                        <input class="form-control" id="author" name="author" type="text"
                            placeholder="e.g. Ahmad Fauzan, Siti Rahma" required>
                        <div class="invalid-feedback">Please enter at least one author.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="publicationDate">Publication Date</label>
                        <input type="date" class="form-control" id="publicationDate" name="publication_date" required>
                        <div class="invalid-feedback">
                            Please select a valid publication date (it cannot be later than today).
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="doiOrLink">DOI / Link</label>
                        <input class="form-control" id="doiOrLink" name="doi_or_link" type="text"
                            placeholder="e.g. 10.1000/example.doi.001 or https://doi.org/..." required>
                        <div class="invalid-feedback">Please enter a DOI or a valid link.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="cover">Cover</label>
                        <input class="form-control" id="cover" name="cover" type="file" accept=".png,.jpg,.jpeg"
                            aria-label="PNG or JPG" required>
                        <div class="invalid-feedback">Please upload a cover image (PNG or JPG).</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="publicationFile">Publication File</label>
                        <input class="form-control" id="publicationFile" name="publication_file" type="file"
                            accept=".pdf" aria-label="PDF" required>
                        <div class="invalid-feedback">Please upload the publication file (PDF only).</div>
                    </div>

                    {{-- No Status field here on purpose — new journals are
                             automatically set to Pending on the backend. --}}

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                        <button class="btn btn-success" type="submit" id="saveBtn">Add Journal</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

{{-- Add Journal confirmation modal — shown only after the form passes
         validation (see script below), per the spec: validate first,
         confirm second, submit third. --}}
<div class="modal fade" id="confirmAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Journal</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to add this journal? It will be submitted with a
                <strong>Pending</strong> status.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmAddBtn">Add Journal</button>
            </div>
        </div>
    </div>
</div>

{{-- Unsaved changes confirmation modal — shown when Cancel is clicked
         and the form has been modified. --}}
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
                <a href="{{ route('admin.journals.index') }}" class="btn btn-danger">Discard Changes</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        "use strict";

        const form = document.getElementById('journalCreateForm');
        const dateInput = document.getElementById('publicationDate');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmAddBtn = document.getElementById('confirmAddBtn');

        // --- Default & max publication date = today ---
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayString = `${yyyy}-${mm}-${dd}`;
        dateInput.max = todayString;
        dateInput.value = todayString;

        // --- Track whether the user has actually changed anything ---
        let formTouched = false;
        form.addEventListener('input', () => {
            formTouched = true;
        });
        form.addEventListener('change', () => {
            formTouched = true;
        });

        // --- Validate on submit; show confirmation modal only if valid ---
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (form.checkValidity()) {
                const modal = coreui.Modal.getOrCreateInstance(document.getElementById('confirmAddModal'));
                modal.show();
            }

            form.classList.add('was-validated');
        }, false);

        // --- Confirmation modal's "Add Journal" actually submits the form ---
        confirmAddBtn.addEventListener('click', function () {
            const modal = coreui.Modal.getInstance(document.getElementById('confirmAddModal'));
            modal.hide();
            // form.submit() bypasses the 'submit' event listener above,
            // so it won't re-trigger the confirmation modal in a loop.
            form.submit();
        });

        // --- Cancel button: warn only if the form was actually touched ---
        cancelBtn.addEventListener('click', function () {
            if (formTouched) {
                const modal = coreui.Modal.getOrCreateInstance(document.getElementById(
                    'unsavedChangesModal'));
                modal.show();
            } else {
                window.location.href = "{{ route('admin.journals.index') }}";
            }
        });

    })();

</script>
@endpush
