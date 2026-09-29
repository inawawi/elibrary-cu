<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BiblioController;
use App\Http\Controllers\Admin\CirculationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\MemberAreaController;
use App\Http\Controllers\OpacController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public OPAC Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [OpacController::class, 'index'])->name('opac.index');
Route::get('/search', [OpacController::class, 'search'])->name('opac.search');
Route::get('/book/{id}', [OpacController::class, 'show'])->name('opac.show');
Route::match(['get', 'post'], '/guestbook', [OpacController::class, 'guestbook'])->name('opac.guestbook');
Route::get('/news', [OpacController::class, 'news'])->name('opac.news');

/*
|--------------------------------------------------------------------------
| Member Area Routes
|--------------------------------------------------------------------------
*/
Route::get('/member/login', [MemberAreaController::class, 'showLoginForm'])->name('member.login');
Route::post('/member/login', [MemberAreaController::class, 'login'])->name('member.login.post');
Route::post('/member/logout', [MemberAreaController::class, 'logout'])->name('member.logout');

Route::middleware('auth:member')->group(function () {
    Route::get('/member/dashboard', [MemberAreaController::class, 'dashboard'])->name('member.dashboard');
});

/*
|--------------------------------------------------------------------------
| Admin / Librarian Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth:web')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Bibliography Management
        Route::get('/biblio', [BiblioController::class, 'index'])->name('admin.biblio.index');
        Route::get('/biblio/create', [BiblioController::class, 'create'])->name('admin.biblio.create');
        Route::post('/biblio', [BiblioController::class, 'store'])->name('admin.biblio.store');
        Route::get('/skripsi/create', [BiblioController::class, 'createSkripsi'])->name('admin.skripsi.create');
        Route::post('/skripsi', [BiblioController::class, 'storeSkripsi'])->name('admin.skripsi.store');
        Route::get('/ebook/create', [BiblioController::class, 'createEbook'])->name('admin.ebook.create');
        Route::post('/ebook', [BiblioController::class, 'storeEbook'])->name('admin.ebook.store');
        Route::get('/biblio/{id}/edit', [BiblioController::class, 'edit'])->name('admin.biblio.edit');
        Route::put('/biblio/{id}', [BiblioController::class, 'update'])->name('admin.biblio.update');
        Route::delete('/biblio/{id}', [BiblioController::class, 'destroy'])->name('admin.biblio.destroy');
        Route::match(['get', 'post'], '/biblio/{id}/items', [BiblioController::class, 'manageItems'])->name('admin.biblio.items');
        Route::delete('/item/{id}', [BiblioController::class, 'deleteItem'])->name('admin.biblio.item.delete');

        // Circulation Management
        Route::get('/circulation', [CirculationController::class, 'index'])->name('admin.circulation.index');
        Route::post('/circulation/loan', [CirculationController::class, 'loan'])->name('admin.circulation.loan');
        Route::post('/circulation/return', [CirculationController::class, 'returnItem'])->name('admin.circulation.return');
        Route::get('/circulation/active', [CirculationController::class, 'activeLoans'])->name('admin.circulation.active');
        Route::get('/circulation/history', [CirculationController::class, 'history'])->name('admin.circulation.history');

        // Membership Management
        Route::get('/member', [MemberController::class, 'index'])->name('admin.member.index');
        Route::get('/member/create', [MemberController::class, 'create'])->name('admin.member.create');
        Route::post('/member', [MemberController::class, 'store'])->name('admin.member.store');
        Route::get('/member/{id}/edit', [MemberController::class, 'edit'])->name('admin.member.edit');
        Route::put('/member/{id}', [MemberController::class, 'update'])->name('admin.member.update');
        Route::get('/member/{id}/card', [MemberController::class, 'showCard'])->name('admin.member.card');
        Route::patch('/member/{id}/toggle-status', [MemberController::class, 'toggleStatus'])->name('admin.member.toggle-status');
        Route::delete('/member/{id}', [MemberController::class, 'destroy'])->name('admin.member.destroy');

        // Master Data
        Route::get('/master/authors', [MasterDataController::class, 'authors'])->name('admin.master.authors');
        Route::post('/master/authors', [MasterDataController::class, 'storeAuthor'])->name('admin.master.authors.store');
        Route::delete('/master/authors/{id}', [MasterDataController::class, 'deleteAuthor'])->name('admin.master.authors.delete');

        Route::get('/master/publishers', [MasterDataController::class, 'publishers'])->name('admin.master.publishers');
        Route::post('/master/publishers', [MasterDataController::class, 'storePublisher'])->name('admin.master.publishers.store');
        Route::delete('/master/publishers/{id}', [MasterDataController::class, 'deletePublisher'])->name('admin.master.publishers.delete');

        Route::get('/master/topics', [MasterDataController::class, 'topics'])->name('admin.master.topics');
        Route::post('/master/topics', [MasterDataController::class, 'storeTopic'])->name('admin.master.topics.store');
        Route::delete('/master/topics/{id}', [MasterDataController::class, 'deleteTopic'])->name('admin.master.topics.delete');

        // Pengaturan & Aturan Perpustakaan
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings/member-types/{id}', [SettingController::class, 'updateMemberType'])->name('admin.settings.member-type.update');
        Route::post('/settings/member-types', [SettingController::class, 'storeMemberType'])->name('admin.settings.member-type.store');
        Route::delete('/settings/member-types/{id}', [SettingController::class, 'deleteMemberType'])->name('admin.settings.member-type.delete');
        Route::post('/settings/announcement', [SettingController::class, 'updateAnnouncement'])->name('admin.settings.announcement.update');
        Route::post('/settings/general', [SettingController::class, 'updateGeneral'])->name('admin.settings.general.update');
        Route::post('/settings/slides', [SettingController::class, 'storeSlide'])->name('admin.settings.slides.store');
        Route::post('/settings/slides/{id}/update', [SettingController::class, 'updateSlide'])->name('admin.settings.slides.update');
        Route::delete('/settings/slides/{id}', [SettingController::class, 'deleteSlide'])->name('admin.settings.slides.delete');
        Route::post('/settings/news', [SettingController::class, 'storeNews'])->name('admin.settings.news.store');
        Route::post('/settings/news/{id}/update', [SettingController::class, 'updateNews'])->name('admin.settings.news.update');
        Route::delete('/settings/news/{id}', [SettingController::class, 'deleteNews'])->name('admin.settings.news.delete');
    });
});
