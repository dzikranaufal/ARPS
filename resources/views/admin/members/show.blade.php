@extends('layouts.admin')
@section('title', 'Detail Member')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Detail Member</h2>
    <a href="{{ route('admin.members.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2 text-center">
                @if($member->foto)
                    <img src="{{ asset('storage/'.$member->foto) }}" width="96" height="96" class="rounded-circle object-fit-cover border">
                @else
                    <div class="bg-secondary-subtle rounded-circle d-inline-flex align-items-center justify-content-center" style="width:96px;height:96px;"><span class="small text-body-secondary">no foto</span></div>
                @endif
            </div>
            <div class="col-md-10">
                <h4>{{ $member->nama }}</h4>
                <p class="mb-1"><strong>Email:</strong> {{ $member->email }}</p>
                <p class="mb-1"><strong>Telepon:</strong> {{ $member->telepon ?? '—' }}</p>
                <p class="mb-1"><strong>Organisasi:</strong> {{ $member->organisasi ?? '—' }}</p>
                <p class="mb-1">
                    <strong>Status:</strong>
                    @if($member->status && $member->status->value==='aktif')<span class="badge bg-success">Aktif</span>@else<span class="badge bg-danger">Nonaktif</span>@endif
                    <span class="ms-2"><strong>Role:</strong> {{ $member->role->value }}</span>
                </p>
                <p class="mb-0"><strong>Jumlah Karya:</strong> {{ $member->publications_count }}</p>
                <form action="{{ route('admin.members.update-status', $member) }}" method="POST" class="mt-3">@csrf @method('PUT')
                    <button type="submit" class="btn btn-sm {{ $member->status && $member->status->value==='aktif' ? 'btn-warning' : 'btn-success' }}">
                        @if($member->status && $member->status->value==='aktif') Nonaktifkan @else Aktifkan @endif
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Karya (Publications)</div>
    <div class="card-body">
        @forelse ($publications as $pub)
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div>
                    <div class="fw-semibold">{{ $pub->judul }}</div>
                    <div class="small text-body-secondary">{{ $pub->kategori->value ?? $pub->kategori }} • {{ $pub->created_at->format('d M Y') }}</div>
                </div>
                <span class="badge {{ $pub->status->value==='approved' ? 'bg-success' : ($pub->status->value==='rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $pub->status->value }}</span>
            </div>
        @empty
            <p class="small text-body-secondary mb-0">Belum ada karya.</p>
        @endforelse
        <div class="mt-3">{{ $publications->links() }}</div>
    </div>
</div>
@endsection
