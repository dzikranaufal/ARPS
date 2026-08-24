<?php

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FocusAreaController;
use App\Http\Controllers\Admin\HeroController;
use App\Http\Controllers\Admin\JournalController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\OrganizationProfileController;
use App\Http\Controllers\Admin\OrganizationStructureController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\PublicationController as AdminPublicationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TechnologyInnovationController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\Member\MemberProfileController;
use App\Http\Controllers\Member\PublicationController as MemberPublicationController;
use App\Models\Event;
use App\Models\FocusArea;
use App\Models\Hero;
use App\Models\Journal;
use App\Models\News;
use App\Models\OrganizationProfile;
use App\Models\OrganizationStructure;
use App\Models\Program;
use App\Models\Publication;
use App\Models\TechnologyInnovation;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
| Manual auth — no Breeze/Jetstream. Login/register/logout wired to
| controllers; logout is POST-only (risk A5).
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
| No prefix, no name prefix — these are the public-facing ARPS site.
| Distinct from admin.* route names below, even where paths look similar
| (e.g. this "journals.index" vs admin's "admin.journals.index").
*/

Route::get('/', function () {
    $heroes = Hero::where('status', 'aktif')->orderBy('urutan')->get();
    $latestNews = News::orderByDesc('tanggal_publish')->limit(2)->get();
    $profile = OrganizationProfile::first();
    return view('home', compact('heroes', 'latestNews', 'profile'));
})->name('home');

Route::get('/about', function () {
    $profile = OrganizationProfile::first();
    $focusAreas = FocusArea::orderBy('urutan')->get();
    return view('about', compact('profile', 'focusAreas'));
})->name('about');

Route::get('/programs', function () {
    $programs = Program::with('kategori')->orderBy('judul')->paginate(9);

    return view('programs.index', compact('programs'));
})->name('programs.index');

Route::get('/programs/{program}', function (Program $program) {
    $program->load('kategori');
    return view('programs.show', compact('program'));
})->name('programs.show');

Route::get('/technology-innovation', function () {
    $innovations = TechnologyInnovation::where('status', 'aktif')->orderByDesc('created_at')->paginate(9);

    return view('technology-innovation.index', compact('innovations'));
})->name('technology-innovation.index');

Route::get('/technology-innovation/{innovation}', function (TechnologyInnovation $innovation) {
    abort_unless($innovation->status->value === 'aktif', 404);
    return view('technology-innovation.show', compact('innovation'));
})->name('technology-innovation.show');

Route::get('/organization', function () {
    $profile = OrganizationProfile::first();
    $structures = OrganizationStructure::orderBy('nama_pengurus')->paginate(12);
    $members = User::where('role', UserRole::Member)
        ->where('status', AccountStatus::Aktif)
        ->orderBy('nama')
        ->paginate(12);

    return view('organization.index', compact('profile', 'structures', 'members'));
})->name('organization.index');

Route::get('/contact', function () {
    $profile = OrganizationProfile::first();

    return view('contact.index', compact('profile'));
})->name('contact.index');

Route::get('/events', function () {
    $events = Event::orderBy('tanggal_waktu')->paginate(9);

    return view('events.index', compact('events'));
})->name('events.index');

Route::get('/events/{event}', function (Event $event) {
    return view('events.show', compact('event'));
})->name('events.show');

Route::get('/publications', function (\Illuminate\Http\Request $request) {
    $query = Publication::with('member')->where('status', 'approved');
    if ($request->filled('kategori')) {
        $kat = $request->string('kategori')->toString();
        if (in_array($kat, ['tulisan', 'prestasi', 'produk', 'pkm'], true)) {
            $query->where('kategori', $kat);
        }
    }
    $publications = $query->latest()->paginate(12)->withQueryString();
    return view('publications.index', compact('publications'));
})->name('publications.index');

Route::get('/publications/{publication}', function (Publication $publication) {
    abort_unless($publication->status->value === 'approved', 404);
    $publication->load('member');
    return view('publications.show', compact('publication'));
})->name('publications.show');

Route::get('/news', function () {
    $news = News::orderByDesc('tanggal_publish')->paginate(9);

    return view('news.index', compact('news'));
})->name('news.index');

Route::get('/news/{news}', function (News $news) {
    return view('news.show', compact('news'));
})->name('news.show');

Route::get('/journals', function () {
    $journals = Journal::where('status', 'aktif')->orderBy('nama')->paginate(12);

    return view('journals.index', ['journals' => $journals]);
})->name('journals.index');

Route::get('/sitemap.xml', function () {
    // Sitemap sederhana — bisa diganti generator jika volume halaman bertambah
    $paths = ['/', '/about', '/organization', '/programs', '/technology-innovation', '/journals', '/publications', '/news', '/events', '/contact', '/register', '/login'];
    $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($paths as $p) {
        $xml .= '<url><loc>' . e(url($p)) . '</loc><lastmod>' . now()->toDateString() . '</lastmod></url>';
    }
    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'text/xml');
})->name('sitemap');

/*
|--------------------------------------------------------------------------
| Member dashboard
|--------------------------------------------------------------------------
| Protected by auth + role:member (risk A1).
*/

Route::middleware(['auth', 'role:member'])->group(function () {
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');
    Route::get('/profile', [MemberProfileController::class, 'edit'])->name('member.profile.edit');
    Route::put('/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
});

Route::prefix('member')->name('member.')->middleware(['auth', 'role:member'])->group(function () {
    Route::get('/publications', [MemberPublicationController::class, 'index'])->name('publications.index');
    Route::get('/publications/create', [MemberPublicationController::class, 'create'])->name('publications.create');
    Route::post('/publications', [MemberPublicationController::class, 'store'])->name('publications.store');
    Route::get('/publications/{publication}/download', [MemberPublicationController::class, 'download'])->name('publications.download');
});

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
| Every route below is prefixed with /admin in the URL, every route name
| is prefixed with "admin.", and every route is protected by
| auth + role:superadmin,admin_manager (risk A1).
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:superadmin,admin_manager'])->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('programs', ProgramController::class)->except(['show']);
    Route::resource('journals', JournalController::class)->except(['show']);
    Route::resource('events', EventController::class)->except(['show']);
    Route::resource('news', NewsController::class)->except(['show']);
    Route::resource('technology-innovations', TechnologyInnovationController::class)->except(['show']);
    Route::resource('structure', OrganizationStructureController::class)->except(['show']);
    Route::resource('heroes', HeroController::class)->except(['show']);
    Route::resource('focus-areas', FocusAreaController::class)->except(['show']);

    Route::get('members', [MemberController::class, 'index'])->name('members.index');
    Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show');
    Route::put('members/{member}/status', [MemberController::class, 'updateStatus'])->name('members.update-status');

    Route::get('publications', [AdminPublicationController::class, 'index'])->name('publications.index');
    Route::get('publications/{publication}', [AdminPublicationController::class, 'show'])->name('publications.show');
    Route::put('publications/{publication}/approve', [AdminPublicationController::class, 'approve'])->name('publications.approve');
    Route::put('publications/{publication}/reject', [AdminPublicationController::class, 'reject'])->name('publications.reject');
    Route::delete('publications/{publication}', [AdminPublicationController::class, 'destroy'])->name('publications.destroy');
    Route::get('publications/{publication}/download', [AdminPublicationController::class, 'download'])->name('publications.download');

    Route::get('organization', [OrganizationProfileController::class, 'edit'])->name('organization.edit');
    Route::put('organization', [OrganizationProfileController::class, 'update'])->name('organization.update');

    Route::post('upload/image', [UploadController::class, 'image'])->name('upload.image');

});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:superadmin'])->group(function () {
    Route::resource('admin-users', AdminUserController::class)->except(['show']);
    Route::put('admin-users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('admin-users.update-status');
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});
