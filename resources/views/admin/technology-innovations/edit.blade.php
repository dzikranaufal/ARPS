@extends('layouts.admin')
@section('title', 'Edit Technology Innovation')
@section('content')
<div class="card">
    <div class="card-header">Edit Technology Innovation</div>
    <div class="card-body">
        <form action="{{ route('admin.technology-innovations.update', $technologyInnovation) }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')
            <div class="col-12">
                <label class="form-label" for="judul">Judul</label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $technologyInnovation->judul) }}" required>
                @error('judul')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Judul wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="quill-editor form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $technologyInnovation->deskripsi) }}</textarea>
                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="status">Status</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="aktif" @selected(old('status', $technologyInnovation->status->value)==='aktif')>Aktif</option>
                    <option value="arsip" @selected(old('status', $technologyInnovation->status->value)==='arsip')>Arsip</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label d-block">Gambar saat ini</label>
                @if($technologyInnovation->gambar)
                    <img src="{{ asset('storage/'.$technologyInnovation->gambar) }}" width="96" class="rounded border mb-2">
                @else
                    <span class="small text-body-secondary">Belum ada gambar</span>
                @endif
                <label class="form-label mt-2" for="gambar">Ganti gambar (optional)</label>
                <input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept=".jpg,.jpeg,.png">
                <div class="form-text">Kosongkan untuk tetap pakai gambar lama.</div>
                @error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.technology-innovations.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
