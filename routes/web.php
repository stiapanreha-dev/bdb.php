<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ZakupkiController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

// Client-side logging
Route::post('/api/log', [LogController::class, 'clientLog'])->name('api.log');

// Main page - Zakupki (public, but limited for unauthenticated)
Route::get('/', [ZakupkiController::class, 'index'])->name('home');

// Zakupki routes
Route::get('/zakupki', [ZakupkiController::class, 'index'])->name('zakupki.index');
Route::get('/zakupki/{id}', [ZakupkiController::class, 'show'])->name('zakupki.show');
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
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::post('/profile/avatar/delete', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::post('/profile/work-contacts', [ProfileController::class, 'updateWorkContacts'])->name('profile.work-contacts.update');
});

// Newsletter routes (for authenticated users)
Route::middleware(['auth', 'module:newsletters'])->group(function () {
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
    Route::post('/newsletters', [NewsletterController::class, 'store'])->name('newsletters.store');
    Route::post('/newsletters/keywords', [NewsletterController::class, 'updateKeywords'])->name('newsletters.keywords');
    Route::post('/newsletters/toggle', [NewsletterController::class, 'toggle'])->name('newsletters.toggle');
    Route::delete('/newsletters', [NewsletterController::class, 'destroy'])->name('newsletters.destroy');
});

// News routes
Route::get('/news', [App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/news/create', [App\Http\Controllers\NewsController::class, 'create'])->name('news.create');
Route::post('/news', [App\Http\Controllers\NewsController::class, 'store'])->name('news.store');
Route::get('/news/{news}', [App\Http\Controllers\NewsController::class, 'show'])->name('news.show');
Route::get('/news/{news}/edit', [App\Http\Controllers\NewsController::class, 'edit'])->name('news.edit');
Route::patch('/news/{news}', [App\Http\Controllers\NewsController::class, 'update'])->name('news.update');
Route::delete('/news/{news}', [App\Http\Controllers\NewsController::class, 'destroy'])->name('news.destroy');

// Ideas routes
Route::middleware(['module:ideas'])->group(function () {
    Route::get('/ideas', [App\Http\Controllers\IdeasController::class, 'index'])->name('ideas.index');
    Route::get('/ideas/create', [App\Http\Controllers\IdeasController::class, 'create'])->name('ideas.create');
    Route::post('/ideas', [App\Http\Controllers\IdeasController::class, 'store'])->name('ideas.store');
});

// Announcements routes (доска объявлений)
Route::middleware(['module:announcements'])->group(function () {
    Route::get('/announcements', [App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
});
Route::middleware(['auth', 'module:announcements'])->group(function () {
    Route::get('/announcements/create', [App\Http\Controllers\AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [App\Http\Controllers\AnnouncementController::class, 'store'])->name('announcements.store');
    // Специфичные маршруты должны идти ПЕРЕД маршрутами с параметрами
    Route::delete('/announcements/bulk-delete', [App\Http\Controllers\AnnouncementController::class, 'bulkDelete'])->name('announcements.bulkDelete');
    Route::get('/announcements/{id}/edit', [App\Http\Controllers\AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::patch('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('/announcements/{id}/inquiry', [App\Http\Controllers\AnnouncementController::class, 'sendInquiry'])->name('announcements.inquiry');

    // Image upload routes for Editor.js
    Route::post('/api/upload-image', [App\Http\Controllers\ImageUploadController::class, 'uploadByFile'])->name('image.upload.file');
    Route::post('/api/upload-image-url', [App\Http\Controllers\ImageUploadController::class, 'uploadByUrl'])->name('image.upload.url');

    // Announcement images upload (up to 5 images)
    Route::post('/api/upload-announcement-images', [App\Http\Controllers\ImageUploadController::class, 'uploadAnnouncementImages'])->name('announcement.images.upload');

    // Shop images upload for Editor.js
    Route::post('/api/upload-shop-image', [App\Http\Controllers\ImageUploadController::class, 'uploadShopImage'])->name('shop.image.upload');
});
Route::middleware(['module:announcements'])->group(function () {
    Route::get('/announcements/{id}', [App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');
});

// Articles routes (статьи)
Route::middleware(['module:articles'])->group(function () {
    Route::get('/articles', [App\Http\Controllers\ArticleController::class, 'index'])->name('articles.index');
});
Route::middleware(['auth', 'module:articles'])->group(function () {
    Route::get('/articles/create', [App\Http\Controllers\ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [App\Http\Controllers\ArticleController::class, 'store'])->name('articles.store');
    // Специфичные маршруты должны идти ПЕРЕД маршрутами с параметрами
    Route::delete('/articles/bulk-delete', [App\Http\Controllers\ArticleController::class, 'bulkDelete'])->name('articles.bulkDelete');
    Route::get('/articles/{id}/edit', [App\Http\Controllers\ArticleController::class, 'edit'])->name('articles.edit');
    Route::patch('/articles/{id}', [App\Http\Controllers\ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{id}', [App\Http\Controllers\ArticleController::class, 'destroy'])->name('articles.destroy');
});
Route::middleware(['module:articles'])->group(function () {
    Route::get('/articles/{id}', [App\Http\Controllers\ArticleController::class, 'show'])->name('articles.show');
});

// Shop routes
Route::middleware(['module:shop'])->group(function () {
    Route::get('/shop', [App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
    Route::get('/shop/category/{slug}', [App\Http\Controllers\ShopController::class, 'category'])->name('shop.category');
    Route::get('/shop/my-purchases', [App\Http\Controllers\ShopController::class, 'myPurchases'])->middleware('auth')->name('shop.my-purchases');
    Route::get('/shop/{slug}/download/{file}', [App\Http\Controllers\ShopController::class, 'downloadFile'])->middleware('auth')->name('shop.download');

    // Cart routes (must be before /shop/{slug})
    Route::get('/shop/cart', [App\Http\Controllers\CartController::class, 'index'])->middleware('auth')->name('shop.cart');
    Route::post('/shop/cart/add/{product}', [App\Http\Controllers\CartController::class, 'add'])->middleware('auth')->name('shop.cart.add');
    Route::patch('/shop/cart/update/{item}', [App\Http\Controllers\CartController::class, 'update'])->middleware('auth')->name('shop.cart.update');
    Route::delete('/shop/cart/remove/{item}', [App\Http\Controllers\CartController::class, 'remove'])->middleware('auth')->name('shop.cart.remove');
    Route::delete('/shop/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->middleware('auth')->name('shop.cart.clear');
    Route::post('/shop/cart/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->middleware('auth')->name('shop.cart.checkout');

    Route::get('/shop/{slug}', [App\Http\Controllers\ShopController::class, 'show'])->name('shop.show');
    Route::post('/shop/{id}/purchase', [App\Http\Controllers\ShopController::class, 'purchase'])->middleware('auth')->name('shop.purchase');
});

// Site catalog routes (public)
Route::middleware(['module:site_catalog'])->group(function () {
    Route::get('/catalog', [App\Http\Controllers\SiteCatalogController::class, 'index'])->name('sites.index');
    Route::get('/catalog/category/{slug}', [App\Http\Controllers\SiteCatalogController::class, 'category'])->name('sites.category');
});

// Site catalog routes (authenticated users)
Route::middleware(['auth', 'module:site_catalog'])->group(function () {
    Route::get('/my-sites', [App\Http\Controllers\SiteController::class, 'mySites'])->name('sites.my');
    Route::get('/catalog/create', [App\Http\Controllers\SiteController::class, 'create'])->name('sites.create');
    Route::post('/catalog', [App\Http\Controllers\SiteController::class, 'store'])->name('sites.store');
    Route::get('/catalog/{id}/edit', [App\Http\Controllers\SiteController::class, 'edit'])->name('sites.edit');
    Route::patch('/catalog/{id}', [App\Http\Controllers\SiteController::class, 'update'])->name('sites.update');
    Route::delete('/catalog/{id}', [App\Http\Controllers\SiteController::class, 'destroy'])->name('sites.destroy');

    // Site image upload routes
    Route::post('/api/upload-site-logo', [App\Http\Controllers\ImageUploadController::class, 'uploadSiteLogo'])->name('site.logo.upload');
    Route::post('/api/upload-site-images', [App\Http\Controllers\ImageUploadController::class, 'uploadSiteImages'])->name('site.images.upload');
    Route::post('/api/upload-site-editor-image', [App\Http\Controllers\ImageUploadController::class, 'uploadSiteEditorImage'])->name('site.editor.image.upload');
});

// Site show route (must be after specific routes)
Route::middleware(['module:site_catalog'])->group(function () {
    Route::get('/catalog/{slug}', [App\Http\Controllers\SiteCatalogController::class, 'show'])->name('sites.show');
});

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

// Ticket routes (for authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/tickets', [App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/message', [App\Http\Controllers\TicketController::class, 'addMessage'])->name('tickets.addMessage');
});

// Admin routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/toggle-admin', [App\Http\Controllers\AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::post('/users/{user}/update-balance', [App\Http\Controllers\AdminController::class, 'updateBalance'])->name('admin.users.update-balance');
    Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.users.delete');

    Route::get('/ideas', [App\Http\Controllers\AdminController::class, 'ideas'])->name('admin.ideas');
    Route::post('/ideas/{idea}/update-status', [App\Http\Controllers\AdminController::class, 'updateIdeaStatus'])->name('admin.ideas.update-status');
    Route::delete('/ideas/{idea}', [App\Http\Controllers\AdminController::class, 'deleteIdea'])->name('admin.ideas.delete');

    Route::get('/sql', [App\Http\Controllers\AdminController::class, 'sql'])->name('admin.sql');
    Route::post('/sql/execute', [App\Http\Controllers\AdminController::class, 'executeQuery'])->name('admin.sql.execute');

    Route::get('/newsletter-settings', [App\Http\Controllers\AdminController::class, 'newsletterSettings'])->name('admin.newsletter-settings');
    Route::post('/newsletter-settings', [App\Http\Controllers\AdminController::class, 'updateNewsletterSettings'])->name('admin.newsletter-settings.update');

    Route::get('/payments', [App\Http\Controllers\AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/newsletters', [App\Http\Controllers\AdminController::class, 'newsletters'])->name('admin.newsletters');

    // Cache management routes
    Route::get('/cache', [App\Http\Controllers\AdminController::class, 'cache'])->name('admin.cache');
    Route::post('/cache/clear', [App\Http\Controllers\AdminController::class, 'clearCache'])->name('admin.cache.clear');

    // Module management routes
    Route::get('/modules', [App\Http\Controllers\AdminController::class, 'modules'])->name('admin.modules');
    Route::post('/modules/toggle', [App\Http\Controllers\AdminController::class, 'updateModuleStatus'])->name('admin.modules.toggle');

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

    // Shop category management routes
    Route::resource('shop/categories', App\Http\Controllers\Admin\ShopCategoryController::class)->names([
        'index' => 'admin.shop.categories.index',
        'create' => 'admin.shop.categories.create',
        'store' => 'admin.shop.categories.store',
        'edit' => 'admin.shop.categories.edit',
        'update' => 'admin.shop.categories.update',
        'destroy' => 'admin.shop.categories.destroy',
    ])->except(['show']);

    // Shop product management routes
    Route::resource('shop/products', App\Http\Controllers\Admin\ShopProductController::class)->names([
        'index' => 'admin.shop.products.index',
        'create' => 'admin.shop.products.create',
        'store' => 'admin.shop.products.store',
        'show' => 'admin.shop.products.show',
        'edit' => 'admin.shop.products.edit',
        'update' => 'admin.shop.products.update',
        'destroy' => 'admin.shop.products.destroy',
    ]);
    Route::post('shop/products/{id}/restore', [App\Http\Controllers\Admin\ShopProductController::class, 'restore'])->name('admin.shop.products.restore');
    Route::delete('shop/products/{product}/files/{file}', [App\Http\Controllers\Admin\ShopProductController::class, 'deleteFile'])->name('admin.shop.products.files.destroy');

    // Shop statistics and purchases
    Route::get('shop/statistics', [App\Http\Controllers\Admin\ShopProductController::class, 'statistics'])->name('admin.shop.statistics');
    Route::get('shop/purchases', [App\Http\Controllers\Admin\ShopProductController::class, 'purchases'])->name('admin.shop.purchases');

    // Site catalog category management routes
    Route::resource('sites/categories', App\Http\Controllers\Admin\SiteCategoryController::class)->names([
        'index' => 'admin.sites.categories.index',
        'create' => 'admin.sites.categories.create',
        'store' => 'admin.sites.categories.store',
        'edit' => 'admin.sites.categories.edit',
        'update' => 'admin.sites.categories.update',
        'destroy' => 'admin.sites.categories.destroy',
    ])->except(['show']);

    // Site moderation routes
    Route::get('sites/moderation', [App\Http\Controllers\Admin\SiteModerationController::class, 'index'])->name('admin.sites.moderation.index');
    Route::get('sites/moderation/create', [App\Http\Controllers\Admin\SiteModerationController::class, 'create'])->name('admin.sites.moderation.create');
    Route::post('sites/moderation', [App\Http\Controllers\Admin\SiteModerationController::class, 'store'])->name('admin.sites.moderation.store');
    Route::get('sites/moderation/{id}', [App\Http\Controllers\Admin\SiteModerationController::class, 'show'])->name('admin.sites.moderation.show');
    Route::post('sites/moderation/{id}/approve', [App\Http\Controllers\Admin\SiteModerationController::class, 'approve'])->name('admin.sites.moderation.approve');
    Route::post('sites/moderation/{id}/reject', [App\Http\Controllers\Admin\SiteModerationController::class, 'reject'])->name('admin.sites.moderation.reject');
    Route::delete('sites/moderation/{id}', [App\Http\Controllers\Admin\SiteModerationController::class, 'destroy'])->name('admin.sites.moderation.destroy');

    // Ticket management routes (admin)
    Route::get('/tickets', [App\Http\Controllers\AdminTicketController::class, 'index'])->name('admin.tickets.index');
    Route::get('/tickets/{ticket}', [App\Http\Controllers\AdminTicketController::class, 'show'])->name('admin.tickets.show');
    Route::patch('/tickets/{ticket}/status', [App\Http\Controllers\AdminTicketController::class, 'updateStatus'])->name('admin.tickets.updateStatus');
    Route::post('/tickets/{ticket}/message', [App\Http\Controllers\AdminTicketController::class, 'addMessage'])->name('admin.tickets.addMessage');
});

// Subscription routes
Route::get('/subscriptions', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::middleware(['auth'])->group(function () {
    Route::post('/subscriptions/{tariff}/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::get('/subscriptions/history', [App\Http\Controllers\SubscriptionController::class, 'history'])->name('subscriptions.history');
});

// Payment routes
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/create', [App\Http\Controllers\PaymentController::class, 'create'])->name('payment.create');
    Route::get('/payment/callback', [App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
    Route::get('/payment/history', [App\Http\Controllers\PaymentController::class, 'history'])->name('payment.history');
    Route::get('/payment/status/{paymentId}', [App\Http\Controllers\PaymentController::class, 'status'])->name('payment.status');
});

// YooKassa webhook (public, no auth)
Route::post('/payment/webhook', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');

require __DIR__.'/auth.php';
