@extends('layouts.member')
@section('title', 'Dashboard')
@section('content')
<h2 class="mb-3">Selamat datang, {{ $member->nama }}!</h2>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Status Akun</h5>
                @if($member->status && $member->status->value === 'aktif')
                    <span class="badge bg-success">Aktif</span>
                @elseif($member->status && $member->status->value === 'nonaktif')
                    <span class="badge bg-danger">Nonaktif</span>
                @else
                    <span class="badge bg-secondary">{{ $member->status?->value ?? '—' }}</span>
                @endif
                <p class="small text-body-secondary mt-2 mb-0">Email: {{ $member->email }}</p>
                @if($member->organisasi)<p class="small text-body-secondary mb-0">Institusi: {{ $member->organisasi }}</p>@endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Profil</h5>
                <p class="small text-body-secondary">Kelola data profil dan foto.</p>
                <a href="{{ route('member.profile.edit') }}" class="btn btn-primary btn-sm">Edit Profil</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Karya (Publications)</h5>
                @forelse ($publications as $pub)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <span class="small">{{ $pub->judul }}</span>
                        <span class="badge bg-secondary">{{ $pub->status->value }}</span>
                    </div>
                @empty
                    <p class="small text-body-secondary mb-0">Belum ada karya. Upload karya tersedia di Fase 5.</p>
                @endforelse
                @if($publications->hasPages())
                    <div class="mt-2">{{ $publications->links() }}</div>
                @endif
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('member.publications.index') }}" class="btn btn-outline-primary btn-sm">Lihat Karya</a>
                    <a href="{{ route('member.publications.create') }}" class="btn btn-primary btn-sm">Upload Karya</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
