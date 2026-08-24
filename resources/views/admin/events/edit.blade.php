@extends('layouts.admin')
@section('title', 'Edit Event')
@section('content')
<div class="card">
    <div class="card-header">Edit Event</div>
    <div class="card-body">
        <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')
            <div class="col-12">
                <label class="form-label" for="judul">Judul</label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $event->judul) }}" required>
                @error('judul')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Judul wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="quill-editor form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tanggal_waktu">Tanggal & Waktu (WIB)</label>
                <input type="datetime-local" class="form-control @error('tanggal_waktu') is-invalid @enderror" id="tanggal_waktu" name="tanggal_waktu" value="{{ old('tanggal_waktu', $event->tanggal_waktu->format('Y-m-d\TH:i')) }}" required>
                @error('tanggal_waktu')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Tanggal wajib diisi.</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="lokasi">Lokasi</label>
                <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi', $event->lokasi) }}">
                @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="info_kontak_pendaftaran">Kontak Pendaftaran</label>
                <input type="text" class="form-control @error('info_kontak_pendaftaran') is-invalid @enderror" id="info_kontak_pendaftaran" name="info_kontak_pendaftaran" value="{{ old('info_kontak_pendaftaran', $event->info_kontak_pendaftaran) }}">
                @error('info_kontak_pendaftaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label d-block">Poster saat ini</label>
                @if($event->poster)
                    <img src="{{ asset('storage/'.$event->poster) }}" width="96" class="rounded border mb-2">
                @else
                    <span class="small text-body-secondary">Belum ada poster</span>
                @endif
                <label class="form-label mt-2" for="poster">Ganti poster (optional)</label>
                <input type="file" class="form-control @error('poster') is-invalid @enderror" id="poster" name="poster" accept=".jpg,.jpeg,.png">
                <div class="form-text">Kosongkan untuk tetap pakai poster lama.</div>
                @error('poster')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
