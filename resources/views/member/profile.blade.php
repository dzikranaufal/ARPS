@extends('layouts.member')
@section('title', 'Edit Profil')
@section('content')
<div class="card">
    <div class="card-header">Edit Profil</div>
    <div class="card-body">
        <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            @csrf @method('PUT')

            <div class="col-12">
                <label class="form-label">Email (tidak dapat diubah)</label>
                <input type="text" class="form-control" value="{{ $member->email }}" disabled>
            </div>

            <div class="col-12">
                <label class="form-label" for="nama">Nama</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $member->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Nama wajib diisi.</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="telepon">Telepon</label>
                <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $member->telepon) }}" required>
                @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Telepon wajib diisi.</div>@enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="organisasi">Organisasi / Institusi</label>
                <input type="text" class="form-control @error('organisasi') is-invalid @enderror" id="organisasi" name="organisasi" value="{{ old('organisasi', $member->organisasi) }}">
                @error('organisasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label class="form-label d-block">Foto saat ini</label>
                @if($member->foto)
                    <img src="{{ asset('storage/'.$member->foto) }}" alt="foto" width="96" height="96" class="rounded-circle object-fit-cover border mb-2">
                @else
                    <span class="small text-body-secondary">Belum ada foto</span>
                @endif
                <label class="form-label mt-2" for="foto">Ganti foto (jpg/png, max 2MB)</label>
                <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                <div class="form-text">Kosongkan untuk tetap pakai foto lama.</div>
                @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr class="my-2">
            <h6>Ganti Password (opsional)</h6>
            <div class="col-12">
                <label class="form-label" for="current_password">Password Saat Ini</label>
                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Wajib diisi jika ingin mengganti password.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password">Password Baru</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('member.dashboard') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
