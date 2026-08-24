@extends('layouts.admin')
@section('title', 'Add Hero')
@section('content')
<div class="card">
    <div class="card-header">Add Hero</div>
    <div class="card-body">
        <form action="{{ route('admin.heroes.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="col-12"><label class="form-label" for="judul">Judul</label><input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>@error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label" for="deskripsi">Deskripsi</label><textarea class="form-control quill-editor @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>@error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="gambar">Gambar (jpg/png/webp 2MB)</label><input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept=".jpg,.jpeg,.png,.webp">@error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="link">Link (opsional URL)</label><input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link') }}">@error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="urutan">Urutan</label><input type="number" class="form-control @error('urutan') is-invalid @enderror" id="urutan" name="urutan" value="{{ old('urutan',0) }}">@error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="status">Status</label><select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required><option value="aktif" @selected(old('status')==='aktif')>Aktif</option><option value="arsip" @selected(old('status')==='arsip')>Arsip</option></select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12 d-flex justify-content-end gap-2"><a href="{{ route('admin.heroes.index') }}" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Add Hero</button></div>
        </form>
    </div>
</div>
@endsection
