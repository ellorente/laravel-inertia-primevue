<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ApptR\DocumentType\DocumentTypeController;


Route::middleware(['auth'])->prefix('apptr')->group(function () {
    Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('document-types.index');
});