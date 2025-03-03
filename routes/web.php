<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Note');
});

Route::get('/', [NoteController::class,'index']);

// Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
// Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
// Route::get('/notes/{note}', [NoteController::class, 'show'])->name('notes.show');
// Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
// Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

Route::resource('notes', NoteController::class); //this line replaces all the commented above lines sabi ni gpt 