@extends('layouts.auth')

@section('title', 'Register')

@section('meta_description', 'Daftar gratis member ARPS — tanpa biaya, langsung aktif, akses dashboard.')
@section('content')

<div class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-group d-block d-md-flex row">
                    <div class="card col-md-6 p-4 mb-0">
                        <div class="card-body">

                            <a href="{{ route('home') }}" class="d-block mb-4 text-decoration-none">
                                <h2 class="fw-bold text-body">ARPS</h2>
                            </a>

                            <h1>Register</h1>
                            <p class="text-body-secondary">Daftar gratis sebagai member ARPS</p>

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="cil-user"></i>
                                    </span>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama"
                                        placeholder="Nama Lengkap" value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="cil-envelope-closed"></i>
                                    </span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                        placeholder="Email" value="{{ old('email') }}" autocomplete="email" required>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="cil-phone"></i>
                                    </span>
                                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon"
                                        placeholder="No. Telepon" value="{{ old('telepon') }}" required>
                                    @error('telepon')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="cil-building"></i>
                                    </span>
                                    <input type="text" class="form-control @error('organisasi') is-invalid @enderror" id="organisasi" name="organisasi"
                                        placeholder="Organisasi/Lembaga (opsional)" value="{{ old('organisasi') }}">
                                    @error('organisasi')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="cil-lock-locked"></i>
                                    </span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                        placeholder="Password" autocomplete="new-password" required>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">
                                        <i class="cil-lock-locked"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Konfirmasi Password"
                                        autocomplete="new-password" required>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary px-4">Daftar</button>
                                    </div>
                                    <div class="col-6 text-end">
                                        <a href="{{ route('login') }}" class="btn btn-link px-0">
                                            Sudah punya akun? Login
                                        </a>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                    <div class="card col-md-6 text-white bg-primary py-5 mb-0"
                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <h2>Gabung ARPS</h2>
                            <p>
                                Gratis, tanpa tier, tanpa masa berlaku. Daftar sekali dan langsung
                                menjadi bagian dari komunitas akademisi, peneliti, dan praktisi.
                            </p>
                            <div>
                                <a href="{{ route('home') }}" class="btn btn-outline-light mt-3">
                                    Back to Home
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
