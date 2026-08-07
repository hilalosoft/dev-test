<?php

use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/bookmarks', [BookmarkController::class, 'index']);

Route::patch('/bookmarks/{bookmark}/archive', [BookmarkController::class, 'archive']);
Route::patch('/bookmarks/{bookmark}/unarchive', [BookmarkController::class, 'unarchive']);

Route::get('/tags', [TagController::class, 'index']);
