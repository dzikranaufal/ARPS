@extends('layouts.admin')
@section('title', 'Add Focus Area')
@section('content')
<div class="card">
    <div class="card-header">Add Focus Area</div>
    <div class="card-body">
        <form action="{{ route('admin.focus-areas.store') }}" method="POST" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="col-12"><label class="form-label" for="judul">Judul</label><input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>@error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label" for="deskripsi">Deskripsi</label><textarea class="form-control quill-editor @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>@error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="icon">Icon (opsional, cil-...)</label><input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}" placeholder="cil-lightbulb">@error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="urutan">Urutan</label><input type="number" class="form-control @error('urutan') is-invalid @enderror" id="urutan" name="urutan" value="{{ old('urutan',0) }}">@error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12 d-flex justify-content-end gap-2"><a href="{{ route('admin.focus-areas.index') }}" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Add</button></div>
        </form>
    </div>
</div>
@endsection
