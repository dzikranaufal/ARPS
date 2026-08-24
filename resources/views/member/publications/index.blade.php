@extends('layouts.member')
@section('title', 'Karya Saya')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Karya Saya</h2>
    <a href="{{ route('member.publications.create') }}" class="btn btn-primary">Upload Karya</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>No</th><th>Judul</th><th>Kategori</th><th>Status</th><th>File</th></tr></thead>
                <tbody>
                    @forelse ($publications as $i => $pub)
                        <tr>
                            <td>{{ $publications->firstItem() + $i }}</td>
                            <td>
                                <div class="fw-semibold">{{ $pub->judul }}</div>
                                @if($pub->deskripsi)<div class="small text-body-secondary">{{ \Illuminate\Support\Str::limit($pub->deskripsi, 80) }}</div>@endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $pub->kategori->value }}</span></td>
                            <td>
                                @if($pub->status->value==='pending')<span class="badge bg-warning text-dark">Pending</span>
                                @elseif($pub->status->value==='approved')<span class="badge bg-success">Approved</span>
                                @else<span class="badge bg-danger">Rejected</span>@endif
                            </td>
                            <td>
                                @if($pub->file)
                                    <a href="{{ route('member.publications.download', $pub) }}" class="btn btn-sm btn-outline-primary">Unduh</a>
                                @else
                                    <span class="small text-body-secondary">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Belum ada karya. <a href="{{ route('member.publications.create') }}">Upload sekarang</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $publications->links() }}</div>
    </div>
</div>
@endsection
