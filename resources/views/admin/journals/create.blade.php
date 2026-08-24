@extends('layouts.admin')
@section('title', 'Add Journal')
@section('content')
<div class="card">
    <div class="card-header">Add Journal</div>
    <div class="card-body">
        <form action="{{ route('admin.journals.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="col-12">
                <label class="form-label" for="nama">Nama</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Nama wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="slug">Slug (kosongkan untuk auto-generate dari nama)</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" placeholder="contoh: motor-automotive-engineering">
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="quill-editor form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="e_issn">E-ISSN</label>
                <input type="text" class="form-control @error('e_issn') is-invalid @enderror" id="e_issn" name="e_issn" value="{{ old('e_issn') }}">
                @error('e_issn')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="aktif" @selected(old('status')==='aktif')>Aktif</option>
                    <option value="arsip" @selected(old('status')==='arsip')>Arsip</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="link_eksternal">Link Eksternal (URL)</label>
                <input type="url" class="form-control @error('link_eksternal') is-invalid @enderror" id="link_eksternal" name="link_eksternal" value="{{ old('link_eksternal') }}" required placeholder="https://...">
                @error('link_eksternal')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Link wajib URL valid.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="cover">Cover (jpg/png, max 2MB)</label>
                <input type="file" class="form-control @error('cover') is-invalid @enderror" id="cover" name="cover" accept=".jpg,.jpeg,.png">
                @error('cover')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.journals.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Journal</button>
            </div>
        </form>
    </div>
</div>
@endsection
