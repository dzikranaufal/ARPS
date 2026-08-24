@extends('layouts.admin')
@section('title', 'Edit Journal')
@section('content')
<div class="card">
    <div class="card-header">Edit Journal</div>
    <div class="card-body">
        <form action="{{ route('admin.journals.update', $journal) }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')
            <div class="col-12">
                <label class="form-label" for="nama">Nama</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $journal->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Nama wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="slug">Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $journal->slug) }}">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Kosongkan untuk auto-generate dari nama saat update.</div>
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="quill-editor form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $journal->deskripsi) }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="e_issn">E-ISSN</label>
                <input type="text" class="form-control @error('e_issn') is-invalid @enderror" id="e_issn" name="e_issn" value="{{ old('e_issn', $journal->e_issn) }}">
                @error('e_issn')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="aktif" @selected(old('status', $journal->status->value)==='aktif')>Aktif</option>
                    <option value="arsip" @selected(old('status', $journal->status->value)==='arsip')>Arsip</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="link_eksternal">Link Eksternal (URL)</label>
                <input type="url" class="form-control @error('link_eksternal') is-invalid @enderror" id="link_eksternal" name="link_eksternal" value="{{ old('link_eksternal', $journal->link_eksternal) }}" required>
                @error('link_eksternal')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Link wajib URL valid.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label d-block">Cover saat ini</label>
                @if($journal->cover)
                    <img src="{{ asset('storage/'.$journal->cover) }}" alt="cover" width="96" class="rounded border mb-2">
                @else
                    <span class="text-body-secondary small">Belum ada cover</span>
                @endif
                <label class="form-label mt-2" for="cover">Ganti cover (optional)</label>
                <input type="file" class="form-control @error('cover') is-invalid @enderror" id="cover" name="cover" accept=".jpg,.jpeg,.png">
                <div class="form-text">Kosongkan untuk tetap pakai cover lama.</div>
                @error('cover')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.journals.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
