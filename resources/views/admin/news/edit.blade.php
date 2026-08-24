@extends('layouts.admin')
@section('title', 'Edit News')
@section('content')
<div class="card">
    <div class="card-header">Edit News</div>
    <div class="card-body">
        <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')
            <div class="col-12">
                <label class="form-label" for="judul">Judul</label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $news->judul) }}" required>
                @error('judul')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Judul wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="isi">Isi (plain text)</label>
                <textarea class="quill-editor form-control @error('isi') is-invalid @enderror" id="isi" name="isi" rows="6" required>{{ old('isi', $news->isi) }}</textarea>
                @error('isi')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Isi wajib diisi.</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="tanggal_publish">Tanggal Publish (WIB)</label>
                <input type="datetime-local" class="form-control @error('tanggal_publish') is-invalid @enderror" id="tanggal_publish" name="tanggal_publish" value="{{ old('tanggal_publish', $news->tanggal_publish->format('Y-m-d\TH:i')) }}" required>
                @error('tanggal_publish')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Tanggal wajib diisi.</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label d-block">Gambar saat ini</label>
                @if($news->gambar)
                    <img src="{{ asset('storage/'.$news->gambar) }}" width="96" class="rounded border mb-2">
                @else
                    <span class="small text-body-secondary">Belum ada gambar</span>
                @endif
                <label class="form-label mt-2" for="gambar">Ganti gambar (optional)</label>
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept=".jpg,.jpeg,.png">
                <div class="form-text">Kosongkan untuk tetap pakai gambar lama.</div>
                @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
