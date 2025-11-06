<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/', '/orgs');
    Route::get('/orgs', fn () => Inertia::render('Orgs/Index'))->name('dashboard');
    Route::get('/orgs/{slug}', fn (string $slug) => Inertia::render('Orgs/Show', [
        'slug' => $slug,
    ]))->name('orgs.show');
    Route::get('/projects/{project}', fn ($project) =>
        Inertia::render('Projects/Show', ['projectId' => (int) $project])
    );
    Route::get('/boards/{board}', fn ($board) =>
        Inertia::render('Boards/Show', ['boardId' => (int) $board])
    );
});

require __DIR__.'/auth.php';
