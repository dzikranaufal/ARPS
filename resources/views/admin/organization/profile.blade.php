@extends('layouts.admin')

@section('title', 'Organization Profile')

@section('content')

    {{-- ================================================
         STATIC SAMPLE VALUES — for layout/design purposes only.
         Backend dev: these will come from the organization's single
         database row, e.g. $organization->name, $organization->vision, etc.
    ================================================= --}}

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">Organization Profile</div>
                <div class="card-body">

                    {{-- Real form (backend dev will add
                         action="{{ route('admin.organization.update') }}" method="POST",
                         @csrf, @method('PUT'), enctype="multipart/form-data" --}}
                    <form class="row g-3 needs-validation" id="profileForm" novalidate>

                        <div class="col-12">
                            <label class="form-label" for="orgName">Organization Name</label>
                            <input class="form-control" id="orgName" name="name" type="text"
                                   value="Academics, Researchers, and Practitioners Society (ARPS)" required>
                            <div class="invalid-feedback">Please enter the organization name.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="shortDescription">Short Description</label>
                            <textarea class="form-control" id="shortDescription" name="short_description" required>A community connecting academics, researchers, and practitioners across engineering, social sciences, and applied research.</textarea>
                            <div class="invalid-feedback">Please enter a short description.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="about">About</label>
                            <textarea class="form-control" id="about" name="about" rows="4" required>ARPS was established to bring together academics, researchers, and practitioners...</textarea>
                            <div class="invalid-feedback">Please enter the About text.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="vision">Vision</label>
                            <textarea class="form-control" id="vision" name="vision" rows="3" required>To become a leading regional network advancing collaborative research and innovation.</textarea>
                            <div class="invalid-feedback">Please enter the Vision statement.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="mission">Mission</label>
                            <textarea class="form-control" id="mission" name="mission" rows="3" required>To connect academics, researchers, and practitioners through publications, events, and shared programs.</textarea>
                            <div class="invalid-feedback">Please enter the Mission statement.</div>
                        </div>

                        {{-- Logo: current file shown, replacing it is optional --}}
                        <div class="col-12">
                            <label class="form-label d-block">Logo</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <img src="{{ asset('assets/sample/logo.png') }}" alt="Current logo"
                                     width="64" height="64" class="rounded border object-fit-contain p-1">
                                <span class="text-body-secondary small">Current logo</span>
                            </div>
                            <label class="form-label" for="logo">Replace logo (optional)</label>
                            <input class="form-control" id="logo" name="logo" type="file"
                                   accept=".png,.jpg,.jpeg" aria-label="PNG or JPG">
                            <div class="invalid-feedback">Please upload a valid image (PNG or JPG).</div>
                            <div class="form-text">Leave empty to keep the current logo.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button class="btn btn-secondary" type="button" id="cancelBtn">Cancel</button>
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

    {{-- Unsaved changes modal — Discard reloads the page (resets to saved
         values) rather than navigating away, since this page has no list
         to return to. --}}
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
                    <button type="button" class="btn btn-danger" id="discardChangesBtn">Discard Changes</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    (function () {
        "use strict";

        const form = document.getElementById('profileForm');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmSaveBtn = document.getElementById('confirmSaveBtn');
        const discardChangesBtn = document.getElementById('discardChangesBtn');

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

        // --- Cancel: warn only if something changed; otherwise reload immediately ---
        cancelBtn.addEventListener('click', function () {
            if (formTouched) {
                const modal = coreui.Modal.getOrCreateInstance(document.getElementById('unsavedChangesModal'));
                modal.show();
            } else {
                window.location.reload();
            }
        });

        // --- Discard Changes: reload the page, discarding edits ---
        discardChangesBtn.addEventListener('click', function () {
            window.location.reload();
        });

    })();
</script>
@endpush