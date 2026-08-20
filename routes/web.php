<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\JournalController;

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
| These routes are for authentication (login, logout, password reset, etc.)
| They are typically provided by Laravel's built-in auth scaffolding or a package like Laravel Breeze or Jetstream.
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
| No prefix, no name prefix — these are the public-facing ARPS site.
| Distinct from admin.* route names below, even where paths look similar
| (e.g. this "journals.index" vs admin's "admin.journals.index").
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/events', function () {
    return view('events.index');
})->name('events.index');

Route::get('/membership', function () {
    return view('membership.index');
})->name('membership.index');

Route::get('/publications', function () {
    return view('publications.index');
})->name('publications.index');

Route::get('/news', function () {
    return view('news.index');
})->name('news.index');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/journals', function () {
    return view('journals.index');
})->name('journals.index');

/*
|--------------------------------------------------------------------------
| Dedicated Journal Microsite
|--------------------------------------------------------------------------
| Conceptually this will live on its own subdomain later, e.g.:
|
|   Route::domain('{journal}.arps.org')->group(function () {
|       Route::get('/', [JournalSiteController::class, 'home']);
|       ...
|   });
|
| For now (no real domain to test subdomains against), we use a path
| parameter instead — {slug} plays the same role {journal} would play
| in the domain() version above. Swapping this later is a routing-file
| change only; the views/controllers underneath don't need to change.
*/
Route::prefix('journal/{slug}')->name('journal.')->group(function () {
    Route::get('/', function ($slug) {
        return view('journal-site.home', ['slug' => $slug]);
    })->name('home');

    Route::get('/archives', function ($slug) {
        return view('journal-site.archives', ['slug' => $slug]);
    })->name('archives');

    Route::get('/guidelines', function ($slug) {
        return view('journal-site.guidelines', ['slug' => $slug]);
    })->name('guidelines');
});

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
| Every route below is prefixed with /admin in the URL, and every route
| name is prefixed with "admin." — e.g. this generates a route reachable
| at /admin/journals, named "admin.journals.index".
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/journals', function () {
        return view('admin.journals.index');
    })->name('journals.index');

    Route::get('/journals/create', function () {
        return view('admin.journals.create');
    })->name('journals.create');

    Route::get('/journals/{id}/edit', function () {
        return view('admin.journals.edit');
    })->name('journals.edit');

    // Organization Profile — single settings-style page, no index/create.
    // Your backend dev will likely add a matching PUT route:
    // Route::put('/organization', [OrganizationController::class, 'update'])->name('organization.update');
    Route::get('/organization', function () {
        return view('admin.organization.profile');
    })->name('organization.edit');
     // Organization Structure — list + add + edit, same CRUD shape as Journals.
    Route::get('/structure', function () {
        return view('admin.organization.structure.index');
    })->name('structure.index');

    Route::get('/structure/create', function () {
        return view('admin.organization.structure.create');
    })->name('structure.create');

    Route::get('/structure/{id}/edit', function () {
        return view('admin.organization.structure.edit');
    })->name('structure.edit');

});