@extends('layouts.app')
@section('title', 'Contact')
@section('meta_description', 'Hubungi ARPS — kontak resmi, alamat, email, dan media sosial.')
@section('content')
<div class="container my-5">
    <h1 class="mb-2">Contact</h1>
    <p class="text-body-secondary mb-4">Hubungi kami untuk informasi lebih lanjut tentang ARPS.</p>

    {{-- Data profil jika ada; kontak tetap STATIC SAMPLE per task 3.9 --}}
    @if($profile)
        <p class="small text-body-secondary mb-4">Organisasi: {{ $profile->nama }}</p>
    @endif

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100"><div class="card-body text-center">
                <i class="cil-envelope-closed fs-1 text-primary mb-2 d-block"></i>
                <h6 class="card-title">Email</h6>
                <p class="card-text small mb-0">info@arps.org</p>
            </div></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100"><div class="card-body text-center">
                <i class="cil-phone fs-1 text-primary mb-2 d-block"></i>
                <h6 class="card-title">Telepon / WhatsApp</h6>
                <p class="card-text small mb-0">+62 812-3456-7890</p>
            </div></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100"><div class="card-body text-center">
                <i class="cil-location-pin fs-1 text-primary mb-2 d-block"></i>
                <h6 class="card-title">Alamat</h6>
                <p class="card-text small mb-0">Universitas Pendidikan Indonesia<br>Bandung, Indonesia</p>
            </div></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100"><div class="card-body text-center">
                <i class="cil-share fs-1 text-primary mb-2 d-block"></i>
                <h6 class="card-title">Social Media</h6>
                <div class="d-flex justify-content-center gap-2">
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="cil-globe-alt"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="cil-share"></i></a>
                    <a href="#" class="btn btn-outline-secondary btn-sm"><i class="cil-envelope-closed"></i></a>
                </div>
            </div></div>
        </div>
    </div>
</div>
@endsection
