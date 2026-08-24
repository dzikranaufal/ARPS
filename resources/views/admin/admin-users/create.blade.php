@extends('layouts.admin')
@section('title', 'Add Admin')
@section('content')
<div class="card">
    <div class="card-header">Add Admin</div>
    <div class="card-body">
        <form action="{{ route('admin.admin-users.store') }}" method="POST" class="row g-3 needs-validation" novalidate>
            @csrf
            <div class="col-12">
                <label class="form-label" for="nama">Nama</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Nama wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Email wajib diisi.</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="role">Role</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="">Pilih role</option>
                    <option value="superadmin" @selected(old('role')==='superadmin')>Super Admin</option>
                    <option value="admin_manager" @selected(old('role')==='admin_manager')>Admin Manager</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Role wajib dipilih.</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password">Password (min 8)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Password wajib diisi.</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('admin.admin-users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Admin</button>
            </div>
        </form>
    </div>
</div>
@endsection
