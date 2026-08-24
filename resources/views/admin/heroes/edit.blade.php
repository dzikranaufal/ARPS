@extends('layouts.admin')
@section('title', 'Edit Hero')
@section('content')
<div class="card">
    <div class="card-header">Edit Hero</div>
    <div class="card-body">
        <form action="{{ route('admin.heroes.update',$hero) }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')
            <div class="col-12"><label class="form-label" for="judul">Judul</label><input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul',$hero->judul) }}" required>@error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label" for="deskripsi">Deskripsi</label><textarea class="form-control quill-editor @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi',$hero->deskripsi) }}</textarea>@error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12">
                <label class="form-label d-block">Gambar saat ini</label>
                @if($hero->gambar)<img src="{{ asset('storage/'.$hero->gambar) }}" width="160" class="rounded border mb-2">@else<span class="small text-body-secondary">Belum ada</span>@endif
                <label class="form-label mt-2" for="gambar">Ganti gambar</label><input type="file" class="form-control @error('gambar') is-invalid @enderror" id="gambar" name="gambar" accept=".jpg,.jpeg,.png,.webp"><div class="form-text">Kosongkan untuk tetap.</div>@error('gambar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6"><label class="form-label" for="link">Link</label><input type="url" class="form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link',$hero->link) }}">@error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="urutan">Urutan</label><input type="number" class="form-control @error('urutan') is-invalid @enderror" id="urutan" name="urutan" value="{{ old('urutan',$hero->urutan) }}">@error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label class="form-label" for="status">Status</label><select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required><option value="aktif" @selected(old('status',$hero->status->value)==='aktif')>Aktif</option><option value="arsip" @selected(old('status',$hero->status->value)==='arsip')>Arsip</option></select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12 d-flex justify-content-end gap-2"><a href="{{ route('admin.heroes.index') }}" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>
@endsection
