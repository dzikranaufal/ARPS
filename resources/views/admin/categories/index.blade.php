@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="cil-plus me-1"></i> Add Category</a>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr><th>No</th><th>Nama</th><th>Programs</th><th class="text-end">Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($categories as $i => $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $i }}</td>
                            <td>{{ $category->nama }}</td>
                            <td><span class="badge bg-secondary">{{ $category->programs_count }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary me-1"><i class="cil-pencil"></i></a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-coreui-toggle="modal" data-coreui-target="#deleteModal{{ $category->id }}"><i class="cil-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $categories->links() }}</div>
    </div>
</div>
@foreach ($categories as $category)
<div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Delete Category</h5><button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">Hapus kategori <strong>{{ $category->nama }}</strong>? Program dengan kategori ini akan kehilangan kategori (menjadi null).</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button type="submit" class="btn btn-danger">Delete</button></form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
