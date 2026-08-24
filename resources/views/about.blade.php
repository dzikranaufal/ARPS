@extends('layouts.app')
@section('title', 'About')
@section('meta_description', 'Tentang ARPS — profil, visi, misi, dan 5 fokus bidang: engineering, sosial, akademik, penelitian, praktik.')
@section('content')
<div class="container my-5">
    <h1 class="mb-4">About ARPS</h1>

    @if($profile)
        <div class="lead">{!! \Mews\Purifier\Facades\Purifier::clean($profile->deskripsi ?? 'ARPS (Academics, Researchers, and Practitioners Society) adalah perkumpulan yang mempertemukan akademisi, peneliti, dan praktisi untuk berkolaborasi, berbagi ilmu, serta mengembangkan program berbasis teknologi.') !!}</div>

        <div class="row g-4 mt-2">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-bold">Visi</div>
                    <div class="card-body">
                        <div class="mb-0">{!! \Mews\Purifier\Facades\Purifier::clean($profile->visi ?? 'Menjadi wadah kolaborasi yang unggul dalam pengembangan ilmu pengetahuan, teknologi, dan praktik profesional yang berdampak bagi masyarakat.') !!}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-bold">Misi</div>
                    <div class="card-body">
                        @if($profile->misi)
                            <div class="mb-0">{!! \Mews\Purifier\Facades\Purifier::clean($profile->misi) !!}</div>
                        @else
                            <ul class="mb-0">
                                <li>Memfasilitasi kolaborasi riset antara akademisi, peneliti, dan praktisi.</li>
                                <li>Menyebarluaskan hasil penelitian dan inovasi melalui publikasi &amp; program.</li>
                                <li>Mendorong pengembangan program berbasis teknologi yang relevan dengan kebutuhan masyarakat.</li>
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <p class="lead">
            ARPS (Academics, Researchers, and Practitioners Society) adalah perkumpulan yang
            mempertemukan akademisi, peneliti, dan praktisi untuk berkolaborasi, berbagi ilmu,
            serta mengembangkan program berbasis teknologi.
        </p>
        <div class="row g-4 mt-2">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-bold">Visi</div>
                    <div class="card-body">
                        <p class="mb-0">Menjadi wadah kolaborasi yang unggul dalam pengembangan ilmu pengetahuan, teknologi, dan praktik profesional yang berdampak bagi masyarakat.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-bold">Misi</div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Memfasilitasi kolaborasi riset antara akademisi, peneliti, dan praktisi.</li>
                            <li>Menyebarluaskan hasil penelitian dan inovasi melalui publikasi &amp; program.</li>
                            <li>Mendorong pengembangan program berbasis teknologi yang relevan dengan kebutuhan masyarakat.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <h2 class="mt-5 mb-3">Fokus Bidang</h2>
    <div class="row g-3">
        @forelse($focusAreas ?? [] as $area)
            <div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body"><h5 class="card-title">{{ $area->judul }}</h5><p class="card-text small mb-0">{!! \Mews\Purifier\Facades\Purifier::clean($area->deskripsi) !!}</p></div></div></div>
        @empty
            <div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body"><h5 class="card-title">Engineering</h5><p class="card-text small mb-0">Inovasi teknik dan rekayasa untuk solusi nyata.</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body"><h5 class="card-title">Sosial</h5><p class="card-text small mb-0">Kajian dan program sosial kemasyarakatan.</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body"><h5 class="card-title">Akademik</h5><p class="card-text small mb-0">Pendidikan, kurikulum, dan pengembangan keilmuan.</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body"><h5 class="card-title">Penelitian</h5><p class="card-text small mb-0">Riset kolaboratif lintas disiplin dan institusi.</p></div></div></div>
            <div class="col-md-6 col-lg-4"><div class="card h-100"><div class="card-body"><h5 class="card-title">Praktik / Profesional</h5><p class="card-text small mb-0">Jembatan antara dunia akademik dan praktik industri.</p></div></div></div>
        @endforelse
    </div>
    <div class="mt-3">
        <a href="{{ route('admin.focus-areas.index') }}" class="small text-body-secondary d-none">Kelola Fokus</a>
    </div>
</div>
@endsection
