@extends('layouts.admin')
@section('title', 'Detail Publication')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Detail Karya</h2>
    <a href="{{ route('admin.publications.index') }}" class="btn btn-secondary">Back</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h4>{{ $publication->judul }}</h4>
        <p class="mb-1"><span class="badge bg-secondary">{{ $publication->kategori->value }}</span>
            @if($publication->status->value==='pending')<span class="badge bg-warning text-dark">Pending</span>
            @elseif($publication->status->value==='approved')<span class="badge bg-success">Approved</span>
            @else<span class="badge bg-danger">Rejected</span>@endif
        </p>
        @if($publication->deskripsi)<div class="mt-2 prose">{{ $publication->deskripsi }}</div>@endif
        <p class="small text-body-secondary mb-1">Uploader: {{ $publication->member->nama ?? '—' }} ({{ $publication->member->email ?? '' }})</p>
        <p class="small text-body-secondary mb-1">Tanggal: {{ $publication->created_at->format('d M Y H:i') }}</p>
        @if($publication->reviewer)<p class="small text-body-secondary mb-1">Reviewer: {{ $publication->reviewer->nama }}</p>@endif
        @if($publication->file)
            <a href="{{ route('admin.publications.download', $publication) }}" class="btn btn-sm btn-outline-primary mt-2">Unduh File</a>
        @endif

        @if($publication->status->value==='pending')
            <div class="mt-3 d-flex gap-2">
                <form action="{{ route('admin.publications.approve', $publication) }}" method="POST">@csrf @method('PUT')<button type="submit" class="btn btn-success">Approve</button></form>
                <form action="{{ route('admin.publications.reject', $publication) }}" method="POST">@csrf @method('PUT')<button type="submit" class="btn btn-danger">Reject</button></form>
            </div>
        @endif
        <form action="{{ route('admin.publications.destroy', $publication) }}" method="POST" class="mt-3" onsubmit="return confirm('Hapus karya ini?')">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger btn-sm">Hapus Karya</button></form>
    </div>
</div>
@endsection
