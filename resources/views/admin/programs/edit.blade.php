@extends('layouts.admin')
@section('title', 'Edit Program')
@section('content')
<div class="card">
    <div class="card-header">Edit Program</div>
    <div class="card-body">
        <form action="{{ route('admin.programs.update', $program) }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')
            <div class="col-12">
                <label class="form-label" for="judul">Judul</label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $program->judul) }}" required>
                @error('judul')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Judul wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="kategori_id">Kategori</label>
                <select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id" required>
                    <option value="">Pilih kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(old('kategori_id', $program->kategori_id) == $cat->id)>{{ $cat->nama }}</option>
                    @endforeach
                </select>
                @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Kategori wajib dipilih.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="quill-editor form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $program->deskripsi) }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label d-block">Gambar saat ini</label>
                @if($program->gambar)
                    <img src="{{ asset('storage/'.$program->gambar) }}" alt="gambar" width="96" class="rounded border mb-2">
                @else
                    <span class="text-body-secondary small">Belum ada gambar</span>
                @endif
                <label class="form-label mt-2" for="gambar">Ganti gambar (optional)</label>
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept=".jpg,.jpeg,.png">
                <div class="form-text">Kosongkan untuk tetap pakai gambar lama.</div>
                @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.programs.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
