<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * TEMPORARY STUB — for frontend development only.
 * Replace with the real controller once the backend teammate's
 * Model/Controller/migration are merged in. View files and route
 * names are already final and won't need to change.
 */
class JournalController extends Controller
{
    public function index()
    {
        // Fake paginator-like object so $journals->links() and
        // $journals->count() don't break the real index view.
        $journals = collect([
            (object) [
                'id' => 1,
                'cover' => null,
                'title' => 'Sample Journal Title One',
                'author' => 'Jane Doe',
                'publication_date' => \Carbon\Carbon::parse('2026-05-10'),
                'doi_or_link' => 'https://doi.org/10.1000/sample1',
                'status' => 'pending',
            ],
            (object) [
                'id' => 2,
                'cover' => null,
                'title' => 'Sample Journal Title Two',
                'author' => 'John Smith',
                'publication_date' => \Carbon\Carbon::parse('2026-03-22'),
                'doi_or_link' => 'https://doi.org/10.1000/sample2',
                'status' => 'published',
            ],
            (object) [
                'id' => 3,
                'cover' => null,
                'title' => 'Sample Journal Title Three',
                'author' => 'Alex Lee',
                'publication_date' => \Carbon\Carbon::parse('2025-11-01'),
                'doi_or_link' => null,
                'status' => 'archived',
            ],
        ]);

        return view('admin.journals.index', ['journals' => $journals]);
    }

    public function create()
    {
        return view('admin.journals.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.journals.index')
            ->with('success', '(Stub) Journal would be added here.');
    }

    public function edit($id)
    {
        $journal = (object) [
            'id' => $id,
            'cover' => null,
            'title' => 'Sample Journal Title One',
            'author' => 'Jane Doe',
            'publication_date' => \Carbon\Carbon::parse('2026-05-10'),
            'publication_file' => null,
            'doi_or_link' => 'https://doi.org/10.1000/sample1',
            'status' => 'pending',
        ];

        return view('admin.journals.edit', compact('journal'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.journals.index')
            ->with('success', '(Stub) Journal would be updated here.');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.journals.index')
            ->with('success', '(Stub) Journal would be deleted here.');
    }

    public function show($id)
    {
        //
    }
}