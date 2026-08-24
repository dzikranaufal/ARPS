@extends('layouts.admin')
@section('title', 'Add Event')
@section('content')
<div class="card">
    <div class="card-header">Add Event</div>
    <div class="card-body">
        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="col-12">
                <label class="form-label" for="judul">Judul</label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>
                @error('judul')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Judul wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="quill-editor form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tanggal_waktu">Tanggal & Waktu (WIB)</label>
                <input type="datetime-local" class="form-control @error('tanggal_waktu') is-invalid @enderror" id="tanggal_waktu" name="tanggal_waktu" value="{{ old('tanggal_waktu') }}" required>
                @error('tanggal_waktu')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Tanggal wajib diisi.</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="lokasi">Lokasi</label>
                <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi') }}">
                @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="info_kontak_pendaftaran">Kontak Pendaftaran (opsional)</label>
                <input type="text" class="form-control @error('info_kontak_pendaftaran') is-invalid @enderror" id="info_kontak_pendaftaran" name="info_kontak_pendaftaran" value="{{ old('info_kontak_pendaftaran') }}" placeholder="https://wa.me/... atau email">
                @error('info_kontak_pendaftaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="poster">Poster (jpg/png, max 2MB)</label>
                <input type="file" class="form-control @error('poster') is-invalid @enderror" id="poster" name="poster" accept=".jpg,.jpeg,.png">
                @error('poster')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Event</button>
            </div>
        </form>
    </div>
</div>
@endsection
