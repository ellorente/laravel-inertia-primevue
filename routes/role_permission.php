<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolePermission\RoleController;
use Inertia\Inertia;


Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/roles', [RoleController::class, 'showRoles'])->name('roles.showRoles');
    /*Route::get('/contacts/show-contacts', [ContactController::class, 'showContacts'])->name('contacts.show-contacts');
    Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');    
    Route::put('/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/destroy/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');*/
});

/*
Route::group(['middleware' => ['permission:create post']], function () {
    Route::get('/posts/create', [PostController::class, 'create']);
}); 

*/