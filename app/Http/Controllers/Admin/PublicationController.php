<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('search')->toString();

        $validStatus = ['pending', 'approved', 'rejected'];
        if ($status && ! in_array($status, $validStatus, true)) {
            $status = '';
        }

        $query = Publication::with(['member', 'reviewer'])->latest();

        if ($status !== '') {
            $query->where('status', $status);
        } elseif (! $request->has('status')) {
            // default antrian pending
            $query->where('status', 'pending');
            $status = 'pending';
        }

        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $query->where('judul', 'like', $like);
        }

        $publications = $query->paginate(15)->withQueryString();

        return view('admin.publications.index', compact('publications', 'status', 'search'));
    }

    public function show(Publication $publication): View
    {
        $publication->load(['member', 'reviewer']);

        return view('admin.publications.show', compact('publication'));
    }

    public function approve(Publication $publication): RedirectResponse
    {
        $updated = DB::transaction(function () use ($publication) {
            return Publication::where('id', $publication->id)
                ->where('status', 'pending')
                ->update(['status' => 'approved', 'reviewer_id' => auth()->id()]);
        });

        if (! $updated) {
            return back()->with('error', 'Karya sudah diproses sebelumnya.');
        }

        return back()->with('success', 'Karya berhasil disetujui.');
    }

    public function reject(Publication $publication): RedirectResponse
    {
        $updated = DB::transaction(function () use ($publication) {
            return Publication::where('id', $publication->id)
                ->where('status', 'pending')
                ->update(['status' => 'rejected', 'reviewer_id' => auth()->id()]);
        });

        if (! $updated) {
            return back()->with('error', 'Karya sudah diproses sebelumnya.');
        }

        return back()->with('success', 'Karya berhasil ditolak.');
    }

    public function destroy(Publication $publication): RedirectResponse
    {
        if ($publication->file) {
            Storage::disk('public')->delete($publication->file);
        }
        $publication->delete();

        return redirect()->route('admin.publications.index')->with('success', 'Karya berhasil dihapus.');
    }

    public function download(Publication $publication): StreamedResponse
    {
        abort_unless($publication->file && Storage::disk('public')->exists($publication->file), 404);

        $ext = pathinfo($publication->file, PATHINFO_EXTENSION);
        $name = 'publication-' . $publication->id . '.' . $ext;

        return Storage::disk('public')->download($publication->file, $name);
    }
}
