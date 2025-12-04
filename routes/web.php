<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController; 
use App\Http\Controllers\SearchController; 
use App\Http\Controllers\BookmarkController; 
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SocialAuthController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES (KHÔNG YÊU CẦU ĐĂNG NHẬP) ---

// Trang chủ, Xem chi tiết & Danh sách chung
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/c/{slug}', [CommunityController::class, 'show'])->name('communities.show');
Route::get('/p/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/u/{id}', [UserController::class, 'show'])->name('users.show');
Route::get('/communities', [CommunityController::class, 'index'])->name('communities.index');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

// Live Search API
Route::get('/api/live-search', [SearchController::class, 'search'])->name('api.search');

// Dashboard mặc định (Chuyển hướng về trang chủ)
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// Social Login
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');


// --- AUTHENTICATED ROUTES (YÊU CẦU ĐĂNG NHẬP) ---
Route::middleware('auth')->group(function () {
    // Quản lý Hồ sơ (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cộng đồng (Communities)
    Route::get('/communities/create', [CommunityController::class, 'create'])->name('communities.create');
    Route::post('/communities', [CommunityController::class, 'store'])->name('communities.store');
    Route::get('/communities/{community}/edit', [CommunityController::class, 'edit'])->name('communities.edit');
    Route::put('/communities/{community}', [CommunityController::class, 'update'])->name('communities.update');
    Route::post('/communities/{community}/join', [CommunityController::class, 'toggleJoin'])->name('communities.join');
    
    // Bài viết (Posts)
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    
    // Upload ảnh (cho TinyMCE)
    Route::post('/upload-image', [PostController::class, 'uploadImage'])->name('upload.image');

    // Bình luận, Vote & Bookmark
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/vote', [VoteController::class, 'vote'])->name('vote');
    Route::post('/posts/{post}/bookmark', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/saved-posts', [BookmarkController::class, 'index'])->name('bookmarks.index');

    // Báo cáo vi phạm (Report)
    Route::post('/report', [ReportController::class, 'store'])->name('reports.store');

    // Hệ thống Thông báo (Notifications)
    Route::get('/notifications/check', [NotificationController::class, 'check'])->name('notifications.check');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});


// --- ADMIN ROUTES (CHỈ ADMIN MỚI VÀO ĐƯỢC) ---
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Quản lý Báo cáo
    Route::get('/reports', [AdminController::class, 'index'])->name('admin.reports');
    Route::post('/reports/{report}', [AdminController::class, 'handle'])->name('admin.handle');

    // Các Trang Quản lý
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/communities', [AdminController::class, 'communities'])->name('admin.communities');
    Route::get('/posts', [AdminController::class, 'posts'])->name('admin.posts');
    Route::get('/banned', [AdminController::class, 'banned'])->name('admin.banned');

    // Hành động Quản trị
    Route::delete('/delete/{type}/{id}', [AdminController::class, 'deleteResource'])->name('admin.delete');
    Route::post('/users/{user}/toggle-ban', [AdminController::class, 'toggleBan'])->name('admin.users.toggleBan');
});

require __DIR__.'/auth.php';