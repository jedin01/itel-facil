<?php

use App\Livewire\AcademicCalendar;
use App\Livewire\CreateThread;
use App\Livewire\GlobalSearch;
use App\Livewire\ListDocuments;
use App\Livewire\ListHelpers;
use App\Livewire\ListThreads;
use App\Livewire\ManageMentorships;
use App\Livewire\RequestMentorship;
use App\Livewire\ShowCourses;
use App\Livewire\ShowThread;
use App\Livewire\UploadDocument;
use Illuminate\Support\Facades\Route;

Route::get('/', ShowCourses::class);

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/documents', ListDocuments::class)
    ->name('documents.index');

Route::get('/documents/upload', UploadDocument::class)
    ->middleware(['auth'])
    ->name('documents.upload');

Route::get('/garimpo', ListHelpers::class)
    ->name('garimpo.index');

Route::get('/mentorship/request', RequestMentorship::class)
    ->middleware(['auth'])
    ->name('mentorship.request');

Route::get('/mentorship/manage', ManageMentorships::class)
    ->middleware(['auth'])
    ->name('mentorship.manage');

Route::get('/calendar', AcademicCalendar::class)
    ->name('calendar.index');

// Forum Routes
Route::prefix('forum')->name('forum.')->group(function () {
    Route::get('/', ListThreads::class)->name('index');
    Route::get('/create', CreateThread::class)->middleware('auth')->name('create');
    Route::get('/{thread}', ShowThread::class)->name('show');
});

Route::get('/search', GlobalSearch::class)->name('search.index');

require __DIR__.'/auth.php';