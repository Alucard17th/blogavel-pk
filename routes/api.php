<?php

declare(strict_types=1);

use Blogavel\Blogavel\Http\Controllers\Api\V1\AuthController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\CategoryController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\CommentController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\Admin\CategoryController as AdminCategoryController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\Admin\CommentController as AdminCommentController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\Admin\MediaController as AdminMediaController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\Admin\PostController as AdminPostController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\Admin\TagController as AdminTagController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\PostController;
use Blogavel\Blogavel\Http\Controllers\Api\V1\TagController;
use Blogavel\Blogavel\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->prefix('api')->group(function () {
    $prefix = config('blogavel.route_prefix', 'blogavel');
    $adminAuth = (string) config('blogavel.api_admin_auth', 'sanctum');
    $adminMiddleware = [];

    if ($adminAuth === 'api_key') {
        $adminMiddleware = [ApiKeyMiddleware::class];
    } elseif ($adminAuth === 'sanctum') {
        if (! class_exists(\Laravel\Sanctum\Sanctum::class) && ! class_exists(\Laravel\Sanctum\HasApiTokens::class)) {
            throw new \RuntimeException('Blogavel API admin auth is set to sanctum but laravel/sanctum is not installed. Install laravel/sanctum or set BLOGAVEL_API_ADMIN_AUTH=api_key.');
        }

        $adminMiddleware = ['auth:sanctum'];
    }

    Route::get($prefix.'/health', function () {
        return response()->json(['ok' => true]);
    })->name('blogavel.api.health');

    Route::prefix($prefix.'/v1')->group(function () use ($adminAuth, $adminMiddleware) {
        if ($adminAuth === 'sanctum') {
            Route::prefix('auth')->group(function () {
                Route::post('login', [AuthController::class, 'login'])->name('blogavel.api.v1.auth.login');
                Route::middleware(['auth:sanctum'])->get('me', [AuthController::class, 'me'])->name('blogavel.api.v1.auth.me');
                Route::middleware(['auth:sanctum'])->post('logout', [AuthController::class, 'logout'])->name('blogavel.api.v1.auth.logout');
            });
        }

        Route::get('posts', [PostController::class, 'index'])->name('blogavel.api.v1.posts.index');
        Route::get('posts/{post}', [PostController::class, 'show'])->name('blogavel.api.v1.posts.show');

        Route::get('categories', [CategoryController::class, 'index'])->name('blogavel.api.v1.categories.index');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('blogavel.api.v1.categories.show');

        Route::get('tags', [TagController::class, 'index'])->name('blogavel.api.v1.tags.index');
        Route::get('tags/{tag}', [TagController::class, 'show'])->name('blogavel.api.v1.tags.show');

        Route::get('posts/{post}/comments', [CommentController::class, 'index'])->name('blogavel.api.v1.comments.index');
        Route::post('posts/{post}/comments', [CommentController::class, 'store'])->name('blogavel.api.v1.comments.store');

        Route::middleware($adminMiddleware)->prefix('admin')->group(function () {
            Route::post('posts', [AdminPostController::class, 'store'])->name('blogavel.api.v1.admin.posts.store');
            Route::put('posts/{post:id}', [AdminPostController::class, 'update'])->name('blogavel.api.v1.admin.posts.update');
            Route::delete('posts/{post:id}', [AdminPostController::class, 'destroy'])->name('blogavel.api.v1.admin.posts.destroy');

            Route::post('categories', [AdminCategoryController::class, 'store'])->name('blogavel.api.v1.admin.categories.store');
            Route::put('categories/{category:id}', [AdminCategoryController::class, 'update'])->name('blogavel.api.v1.admin.categories.update');
            Route::delete('categories/{category:id}', [AdminCategoryController::class, 'destroy'])->name('blogavel.api.v1.admin.categories.destroy');

            Route::post('tags', [AdminTagController::class, 'store'])->name('blogavel.api.v1.admin.tags.store');
            Route::put('tags/{tag:id}', [AdminTagController::class, 'update'])->name('blogavel.api.v1.admin.tags.update');
            Route::delete('tags/{tag:id}', [AdminTagController::class, 'destroy'])->name('blogavel.api.v1.admin.tags.destroy');

            Route::post('media', [AdminMediaController::class, 'store'])->name('blogavel.api.v1.admin.media.store');
            Route::delete('media/{medium}', [AdminMediaController::class, 'destroy'])->name('blogavel.api.v1.admin.media.destroy');

            Route::get('comments', [AdminCommentController::class, 'index'])->name('blogavel.api.v1.admin.comments.index');
            Route::post('comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('blogavel.api.v1.admin.comments.approve');
            Route::post('comments/{comment}/spam', [AdminCommentController::class, 'spam'])->name('blogavel.api.v1.admin.comments.spam');
            Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('blogavel.api.v1.admin.comments.destroy');
        });
    });
});
