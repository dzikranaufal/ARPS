@extends('layouts.admin')

@section('title', 'Edit Journal')

@section('content')

    {{-- ================================================
         STATIC SAMPLE DATA — for layout/design purposes only.
         Backend dev: this entire block of hardcoded values will be
         replaced by data pulled from the database via the route
         model binding, e.g.:

         Route::get('/journals/{journal}/edit', [JournalController::class, 'edit'])
         ...
         public function edit(Journal $journal) { return view('admin.journals.edit', compact('journal')); }

         Then every value below becomes $journal->title, $journal->author, etc.
    ================================================= --}}

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">Edit Journal</div>
                <div class="card-body">

                    {{-- Real form (backend dev will add
                         action="{{ route('admin.journals.update', 1) }}" method="POST",
                         @csrf, @method('PUT'), enctype="multipart/form-data" --}}
                    <form class="row g-3 needs-validation" id="journalEditForm" novalidate>

                        <div class="col-12">
                            <label class="form-label" for="title">Title</label>
                            <input class="form-control" id="title" name="title" type="text"
                                   value="Artificial Intelligence for Sustainable Education" required>
                            <div class="invalid-feedback">Please enter a title.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="author">Author</label>
                            <input class="form-control" id="author" name="author" type="text"
                                   value="Ahmad Fauzan, Siti Rahma, Dimas Pratama" required>
                            <div class="invalid-feedback">Please enter at least one author.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="publicationDate">Publication Date</label>
                            <input type="date" class="form-control" id="publicationDate" name="publication_date"
                                   value="2026-08-15" required>
                            <div class="invalid-feedback">
                                Please select a valid publication date (it cannot be later than today).
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="doiOrLink">DOI / Link</label>
                            <input class="form-control" id="doiOrLink" name="doi_or_link" type="text"
                                   value="aise.arps.org" required>
                            <div class="invalid-feedback">Please enter a DOI or a valid link.</div>
                        </div>

                        {{-- Cover: current file shown, replacing it is optional --}}
                        <div class="col-12">
                            <label class="form-label d-block">Cover</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="{{ asset('assets/sample/cover1.jpg') }}" alt="Current cover"
                                     width="48" height="64" class="rounded object-fit-cover border">
                                <span class="text-body-secondary small">Current cover</span>
                            </div>
                            <label class="form-label" for="cover">Replace cover (optional)</label>
                            <input class="form-control" id="cover" name="cover" type="file"
                                   accept=".png,.jpg,.jpeg" aria-label="PNG or JPG">
                            <div class="invalid-feedback">Please upload a valid image (PNG or JPG).</div>
                            <div class="form-text">Leave empty to keep the current cover.</div>
                        </div>

                        {{-- Publication File: current file shown, replacing it is optional --}}
                        <div class="col-12">
                            <label class="form-label d-block">Publication File</label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="cil-file"></i>
                                <a href="#" target="_blank" class="small">current-publication-file.pdf</a>
                            </div>
                            <label class="form-label" for="publicationFile">Replace publication file (optional)</label>
                            <input class="form-control" id="publicationFile" name="publication_file" type="file"
                                   accept=".pdf" aria-label="PDF">
                            <div class="invalid-feedback">Please upload a valid PDF file.</div>
                            <div class="form-text">Leave empty to keep the current file.</div>
                        </div>

                        {{-- Status — visible on Edit only, per spec. New journals
                             default to Pending and can't set this on the Add form. --}}
                        <div class="col-12">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="active" selected>Active</option>
                                <option value="archived">Archived</option>
                            </select>
                            <div class="form-text">
                                Setting this to <strong>Archived</strong> keeps the journal in the database
                                but removes it from the active/public listing.
                            </div>
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
    <div class="modal fade" id="confirmSaveModal" data-coreui-backdrop="static" data-coreui-keyboard="false"
         tabindex="-1" aria-hidden="true">
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

        const form = document.getElementById('journalEditForm');
        const dateInput = document.getElementById('publicationDate');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmSaveBtn = document.getElementById('confirmSaveBtn');

        // --- Max publication date = today (existing value is kept, not overwritten) ---
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        dateInput.max = `${yyyy}-${mm}-${dd}`;

        // --- Track changes ---
        let formTouched = false;
        form.addEventListener('input', () => { formTouched = true; });
        form.addEventListener('change', () => { formTouched = true; });

        // --- Validate, then confirm, then submit ---
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

        // --- Cancel: warn only if something changed ---
        cancelBtn.addEventListener('click', function () {
            if (formTouched) {
                const modal = coreui.Modal.getOrCreateInstance(document.getElementById('unsavedChangesModal'));
                modal.show();
            } else {
                window.location.href = "{{ route('admin.journals.index') }}";
            }
        });

    })();
</script>
@endpush