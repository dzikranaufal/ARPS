@extends('layouts.auth')

@section('title', 'Login')

@section('meta_description', 'Masuk akun ARPS — login member dan admin dengan keamanan berlapis.')
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

                            <h1>Login</h1>
                            <p class="text-body-secondary">Sign in to your ARPS account</p>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                @error('email')
                                    <div class="alert alert-danger py-2 small">{{ $message }}</div>
                                @enderror

                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="cil-user"></i>
                                    </span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                        placeholder="Email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="input-group mb-4">
                                    <span class="input-group-text">
                                        <i class="cil-lock-locked"></i>
                                    </span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                        placeholder="Password" autocomplete="current-password" required>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary px-4">Login</button>
                                    </div>
                                    <div class="col-6 text-end">
                                        <button type="button" class="btn btn-link px-0">
                                            Forgot password?
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="mt-3 text-center small">
                                Belum punya akun?
                                <a href="{{ route('register') }}" class="fw-bold">Daftar</a>
                            </div>

                        </div>
                    </div>

                    <div class="card col-md-6 text-white bg-primary py-5 mb-0"
                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <h2>Welcome to ARPS</h2>
                            <p>
                                Academics, Researchers, and Practitioners Society — connecting
                                academics, researchers, and practitioners nationally and internationally.
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
