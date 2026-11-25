<?php

use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\RootController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', RootController::class);

Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::prefix('{locale}')
    ->whereIn('locale', config('portfolio.locales.supported'))
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    });

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminProjectController::class, 'index'])->name('projects.index');
    Route::get('/create', [AdminProjectController::class, 'create'])->name('projects.create');
    Route::post('/', [AdminProjectController::class, 'store'])->name('projects.store');
    Route::get('/{project}/edit', [AdminProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/{project}', [AdminProjectController::class, 'update'])->name('projects.update');
    Route::delete('/{project}', [AdminProjectController::class, 'destroy'])->name('projects.destroy');
});

require __DIR__.'/auth.php';
