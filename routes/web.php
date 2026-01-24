<?php

declare(strict_types=1);

use Blogavel\Blogavel\Http\Controllers\Admin\CategoryController;
use Blogavel\Blogavel\Http\Controllers\Admin\CommentController as AdminCommentController;
use Blogavel\Blogavel\Http\Controllers\Admin\MediaController;
use Blogavel\Blogavel\Http\Controllers\Admin\PostController as AdminPostController;
use Blogavel\Blogavel\Http\Controllers\Admin\TagController;
use Blogavel\Blogavel\Http\Controllers\CommentController;
use Blogavel\Blogavel\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    $prefix = config('blogavel.route_prefix', 'blogavel');
    $postsPrefix = config('blogavel.public_posts_prefix', 'posts');

    Route::get($prefix, function () {
        return view('blogavel::index');
    })->name('blogavel.home');

    Route::prefix($prefix)->group(function () use ($postsPrefix) {
        Route::get($postsPrefix, [PostController::class, 'index'])->name('blogavel.posts.index');
        Route::get($postsPrefix.'/{post}', [PostController::class, 'show'])->name('blogavel.posts.show');

        Route::post($postsPrefix.'/{post}/comments', [CommentController::class, 'store'])->name('blogavel.comments.store');
    });

    $adminPrefix = config('blogavel.admin_prefix', 'admin');
    $adminMiddleware = config('blogavel.admin_middleware', ['web', 'auth']);

    if ((bool) config('blogavel.manage_blog_gate', false)) {
        $adminMiddleware = array_values(array_unique(array_merge((array) $adminMiddleware, ['can:manage-blog'])));
    }

    Route::middleware($adminMiddleware)->prefix($prefix.'/'.$adminPrefix)->group(function () {
        Route::get('posts', [AdminPostController::class, 'index'])->name('blogavel.admin.posts.index');
        Route::get('posts/create', [AdminPostController::class, 'create'])->name('blogavel.admin.posts.create');
        Route::post('posts', [AdminPostController::class, 'store'])->name('blogavel.admin.posts.store');

        Route::get('posts/{post:id}/edit', [AdminPostController::class, 'edit'])->name('blogavel.admin.posts.edit');
        Route::put('posts/{post:id}', [AdminPostController::class, 'update'])->name('blogavel.admin.posts.update');
        Route::delete('posts/{post:id}', [AdminPostController::class, 'destroy'])->name('blogavel.admin.posts.destroy');

        Route::get('categories', [CategoryController::class, 'index'])->name('blogavel.admin.categories.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('blogavel.admin.categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('blogavel.admin.categories.store');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('blogavel.admin.categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('blogavel.admin.categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('blogavel.admin.categories.destroy');

        Route::get('tags', [TagController::class, 'index'])->name('blogavel.admin.tags.index');
        Route::get('tags/create', [TagController::class, 'create'])->name('blogavel.admin.tags.create');
        Route::post('tags', [TagController::class, 'store'])->name('blogavel.admin.tags.store');
        Route::get('tags/{tag}/edit', [TagController::class, 'edit'])->name('blogavel.admin.tags.edit');
        Route::put('tags/{tag}', [TagController::class, 'update'])->name('blogavel.admin.tags.update');
        Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('blogavel.admin.tags.destroy');

        Route::get('media', [MediaController::class, 'index'])->name('blogavel.admin.media.index');
        Route::post('media', [MediaController::class, 'store'])->name('blogavel.admin.media.store');
        Route::delete('media/{medium}', [MediaController::class, 'destroy'])->name('blogavel.admin.media.destroy');

        Route::get('comments', [AdminCommentController::class, 'index'])->name('blogavel.admin.comments.index');
        Route::post('comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('blogavel.admin.comments.approve');
        Route::post('comments/{comment}/spam', [AdminCommentController::class, 'spam'])->name('blogavel.admin.comments.spam');
        Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('blogavel.admin.comments.destroy');
    });
});
