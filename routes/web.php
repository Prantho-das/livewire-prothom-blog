<?php

use App\Livewire\Front\CategoryPage;
use App\Livewire\Front\EpaperPage;
use App\Livewire\Front\HomePage;
use App\Livewire\Front\PostPage;
use App\Livewire\Front\SearchPage;
use Illuminate\Support\Facades\Route;

Route::livewire('/', HomePage::class)->name('home');
Route::livewire('/search', SearchPage::class)->name('search');
Route::livewire('/epaper', EpaperPage::class)->name('epaper');
Route::livewire('/category/{slug}', CategoryPage::class)->name('category');
Route::livewire('/news/{slug}', PostPage::class)->name('post');
