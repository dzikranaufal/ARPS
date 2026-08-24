@extends('layouts.admin')
@section('title', 'Edit Category')
@section('content')
<div class="card">
    <div class="card-header">Edit Category</div>
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')
            <div class="col-12">
                <label class="form-label" for="nama">Nama</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $category->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Nama wajib diisi.</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
