@extends('layouts.admin')
@section('title', 'Add Member')
@section('content')
<div class="card">
    <div class="card-header">Add Member</div>
    <div class="card-body">
        <form action="{{ route('admin.structure.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="col-12">
                <label class="form-label" for="nama_pengurus">Nama Pengurus</label>
                <input type="text" class="form-control @error('nama_pengurus') is-invalid @enderror" id="nama_pengurus" name="nama_pengurus" value="{{ old('nama_pengurus') }}" required>
                @error('nama_pengurus')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Nama wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="jabatan">Jabatan</label>
                <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required>
                @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Jabatan wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="afiliasi">Afiliasi</label>
                <input type="text" class="form-control @error('afiliasi') is-invalid @enderror" id="afiliasi" name="afiliasi" value="{{ old('afiliasi') }}">
                @error('afiliasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="foto">Foto (jpg/png, max 2MB)</label>
                <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.structure.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Member</button>
            </div>
        </form>
    </div>
</div>
@endsection
