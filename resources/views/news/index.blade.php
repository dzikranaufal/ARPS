@extends('layouts.app')
@section('title', 'News')
@section('meta_description', 'Berita ARPS — informasi kegiatan, kolaborasi, dan pencapaian member.')
@section('content')
<div class="container my-5">
    <h1 class="mb-4">News</h1>

    @forelse ($news as $item)
        <a href="{{ route('news.show', $item) }}" class="text-decoration-none text-body">
            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-md-4">
                        @if($item->gambar)
                            <img src="{{ asset('storage/'.$item->gambar) }}" class="img-fluid h-100" style="object-fit:cover;min-height:160px;" alt="{{ $item->judul }}">
                        @else
                            <div class="bg-secondary-subtle h-100 d-flex align-items-center justify-content-center" style="min-height: 160px;">
                                <span class="text-body-secondary small">[ image ]</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->judul }}</h5>
                            <p class="card-text text-body-secondary small mb-1">{{ $item->tanggal_publish->timezone('Asia/Jakarta')->format('d M Y') }}</p>
                            <p class="card-text">{{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 160) }}</p>
                            <span class="btn btn-sm btn-outline-primary">Read More</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <p class="text-body-secondary">Belum ada berita.</p>
    @endforelse

    <div class="mt-4">{{ $news->links() }}</div>
</div>
@endsection
