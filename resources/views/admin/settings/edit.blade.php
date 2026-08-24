@extends('layouts.admin')
@section('title', 'General Settings')
@section('content')
<div class="card mb-4">
    <div class="card-header">General Settings — Branding</div>
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')
            <div class="col-12">
                <label class="form-label" for="nama">Nama Organisasi</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $organization->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Nama wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="quill-editor form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $organization->deskripsi) }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="visi">Visi</label>
                <textarea class="quill-editor form-control @error('visi') is-invalid @enderror" id="visi" name="visi" rows="3">{{ old('visi', $organization->visi) }}</textarea>
                @error('visi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="misi">Misi</label>
                <textarea class="quill-editor form-control @error('misi') is-invalid @enderror" id="misi" name="misi" rows="3">{{ old('misi', $organization->misi) }}</textarea>
                @error('misi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label d-block">Logo saat ini</label>
                @if($organization->logo)
                    <img src="{{ asset('storage/'.$organization->logo) }}" width="96" height="96" class="rounded border object-fit-contain p-1 mb-2">
                @else
                    <span class="small text-body-secondary">Belum ada logo</span>
                @endif
                <label class="form-label mt-2" for="logo">Ganti logo (jpg/png, max 2MB)</label>
                <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept=".jpg,.jpeg,.png">
                <div class="form-text">Kosongkan untuk tetap pakai logo lama.</div>
                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Kontak (STATIC SAMPLE)</div>
    <div class="card-body">
        <p class="small text-body-secondary mb-0">Bagian kontak (email/telepon/alamat) belum ada kolom di `organization_profile` — ditampilkan sebagai placeholder. Isi di bawah tidak tersimpan ke DB.</p>
        <div class="row g-3 mt-1">
            <div class="col-md-4"><label class="form-label">Email</label><input type="text" class="form-control" value="info@arps.org" disabled></div>
            <div class="col-md-4"><label class="form-label">Telepon</label><input type="text" class="form-control" value="+62 812-3456-7890" disabled></div>
            <div class="col-md-4"><label class="form-label">Alamat</label><input type="text" class="form-control" value="Universitas Pendidikan Indonesia, Bandung" disabled></div>
        </div>
    </div>
</div>
@endsection
