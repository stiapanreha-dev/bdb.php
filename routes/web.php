<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ZakupkiController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

// Main page - Zakupki (public, but limited for unauthenticated)
Route::get('/', [ZakupkiController::class, 'index'])->name('home');

// Zakupki routes
Route::get('/zakupki', [ZakupkiController::class, 'index'])->name('zakupki.index');
Route::get('/zakupki/{id}', [ZakupkiController::class, 'show'])->middleware('auth')->name('zakupki.show');
Route::get('/zakupki/export', [ZakupkiController::class, 'export'])->middleware('auth')->name('zakupki.export');

// Companies routes (authentication required)
Route::middleware('auth')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/{id}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('/companies/export', [CompanyController::class, 'export'])->name('companies.export');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// News routes
Route::get('/news', [App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/news/create', [App\Http\Controllers\NewsController::class, 'create'])->name('news.create');
Route::post('/news', [App\Http\Controllers\NewsController::class, 'store'])->name('news.store');
Route::delete('/news/{news}', [App\Http\Controllers\NewsController::class, 'destroy'])->name('news.destroy');

// Ideas routes
Route::get('/ideas', [App\Http\Controllers\IdeasController::class, 'index'])->name('ideas.index');
Route::get('/ideas/create', [App\Http\Controllers\IdeasController::class, 'create'])->name('ideas.create');
Route::post('/ideas', [App\Http\Controllers\IdeasController::class, 'store'])->name('ideas.store');

Route::get('/invite', function () {
    return '<h1>Пригласи друга</h1><p>Страница в разработке</p>';
})->name('invite');

Route::get('/support', function () {
    return view('static.support');
})->name('support');

// Static pages
Route::get('/privacy-policy', function () {
    return view('static.privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('static.terms-of-service');
})->name('terms-of-service');

Route::get('/offer', function () {
    return view('static.offer');
})->name('offer');

Route::get('/contacts', function () {
    return view('static.contacts');
})->name('contacts');

Route::get('/tariffs', function () {
    return view('static.tariffs');
})->name('tariffs');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/toggle-admin', [App\Http\Controllers\AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::post('/users/{user}/update-balance', [App\Http\Controllers\AdminController::class, 'updateBalance'])->name('admin.users.update-balance');

    Route::get('/ideas', [App\Http\Controllers\AdminController::class, 'ideas'])->name('admin.ideas');
    Route::post('/ideas/{idea}/update-status', [App\Http\Controllers\AdminController::class, 'updateIdeaStatus'])->name('admin.ideas.update-status');
    Route::delete('/ideas/{idea}', [App\Http\Controllers\AdminController::class, 'deleteIdea'])->name('admin.ideas.delete');

    Route::get('/sql', [App\Http\Controllers\AdminController::class, 'sql'])->name('admin.sql');
    Route::post('/sql/execute', [App\Http\Controllers\AdminController::class, 'executeQuery'])->name('admin.sql.execute');

    // Tariff management routes
    Route::resource('tariffs', App\Http\Controllers\Admin\TariffController::class)->names([
        'index' => 'admin.tariffs.index',
        'create' => 'admin.tariffs.create',
        'store' => 'admin.tariffs.store',
        'show' => 'admin.tariffs.show',
        'edit' => 'admin.tariffs.edit',
        'update' => 'admin.tariffs.update',
        'destroy' => 'admin.tariffs.destroy',
    ]);
});

// Subscription routes
Route::middleware(['auth'])->group(function () {
    Route::get('/subscriptions', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/{tariff}/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::get('/subscriptions/history', [App\Http\Controllers\SubscriptionController::class, 'history'])->name('subscriptions.history');
});

// Payment routes (stubs)
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/create', function () {
        return redirect()->back()->with('info', 'Оплата в разработке');
    })->name('payment.create');
});

require __DIR__.'/auth.php';
