@extends('layouts.app')
@section('title', $news->judul)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($news->isi), 150))
@section('content')
<div class="container my-5">
    <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Kembali ke News</a>
    <div class="row">
        <div class="col-lg-8">
            @if($news->gambar)
                <img src="{{ asset('storage/'.$news->gambar) }}" alt="{{ $news->judul }}" class="img-fluid rounded mb-4" style="max-height:400px;object-fit:cover;width:100%;">
            @endif
            <h1 class="mb-2">{{ $news->judul }}</h1>
            <p class="small text-body-secondary mb-3">{{ $news->tanggal_publish->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</p>
            <div class="prose">{!! \Mews\Purifier\Facades\Purifier::clean($news->isi) !!}</div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Berita Lain</div>
                <div class="list-group list-group-flush">
                    @foreach(\App\Models\News::orderByDesc('tanggal_publish')->limit(5)->get() as $other)
                        <a href="{{ route('news.show',$other) }}" class="list-group-item list-group-item-action small">{{ $other->judul }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
