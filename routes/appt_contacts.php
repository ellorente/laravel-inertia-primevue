<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ApptR\contacts\ContactController;

Route::middleware(['auth'])->prefix('apptr')->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/show-contacts', [ContactController::class, 'showContacts'])->name('contacts.show-contacts');
    Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');    
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/destroy/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
});