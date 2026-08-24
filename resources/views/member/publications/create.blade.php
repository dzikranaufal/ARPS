@extends('layouts.member')
@section('title', 'Upload Karya')
@section('content')
<div class="card">
    <div class="card-header">Upload Karya</div>
    <div class="card-body">
        <form action="{{ route('member.publications.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="col-12">
                <label class="form-label" for="judul">Judul</label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" required>
                @error('judul')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Judul wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi (opsional, max 2000)</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="kategori">Kategori</label>
                <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
                    <option value="">Pilih kategori</option>
                    <option value="tulisan" @selected(old('kategori')==='tulisan')>Tulisan</option>
                    <option value="prestasi" @selected(old('kategori')==='prestasi')>Prestasi</option>
                    <option value="produk" @selected(old('kategori')==='produk')>Produk</option>
                    <option value="pkm" @selected(old('kategori')==='pkm')>PkM</option>
                </select>
                @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Kategori wajib dipilih.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="file">File (PDF/JPG/PNG/DOCX, max 10MB)</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.docx" required>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">File wajib diisi.</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('member.publications.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Kirim Karya</button>
            </div>
        </form>
    </div>
</div>
@endsection
