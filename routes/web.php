<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AngularController;
use App\Http\Controllers\InspectionPDFController;
use App\Livewire\Checklists\Dashboard;
use App\Livewire\Checklists\ChecklistIndex;
use App\Livewire\Checklists\ChecklistBuilder;
use App\Livewire\Checklists\ChecklistFiller;
use App\Livewire\Checklists\InspectionHistory;
use App\Livewire\Checklists\InspectionShow;
use App\Livewire\Auth\Login;
use App\Livewire\Categories\CategoryIndex;
use App\Livewire\Categories\CategoryBuilder as CategoryBuilderComponent;
use App\Livewire\Categories\SubcategoryManager;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/login', Login::class)->name('login');

/*
|--------------------------------------------------------------------------
| Rutas de Checklists Dinámicos (Protegidas)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    Route::prefix('checklists')->group(function () {
        Route::get('/', ChecklistIndex::class)->name('checklists.index');
        
        // Solo Admin puede crear/editar
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/create', ChecklistBuilder::class)->name('checklists.create');
            Route::get('/{id}/edit', ChecklistBuilder::class)->name('checklists.edit');
        });
        
        // Admin y Técnico pueden llenar
        Route::middleware(['role:Administrador,Técnico'])->group(function () {
            Route::get('/{id}/fill', ChecklistFiller::class)->name('checklists.fill');
        });
    });

    Route::prefix('inspections')->group(function () {
        Route::get('/', InspectionHistory::class)->name('inspections.index');
        Route::get('/{id}', InspectionShow::class)->name('inspections.show');
        Route::get('/{id}/pdf', [InspectionPDFController::class, 'download'])->name('inspections.pdf');
        Route::get('/{id}/pdf/preview', [InspectionPDFController::class, 'preview'])->name('inspections.pdf.preview');
    });

    // Rutas de Categorías (Solo Admin)
    Route::middleware(['role:Administrador'])->prefix('categories')->group(function () {
        Route::get('/', CategoryIndex::class)->name('categories.index');
        Route::get('/create', CategoryBuilderComponent::class)->name('categories.create');
        Route::get('/{id}/edit', CategoryBuilderComponent::class)->name('categories.edit');
        Route::get('/{category}/subcategories', SubcategoryManager::class)->name('categories.subcategories');
    });

    // Rutas de Empresas (Solo Admin)
    Route::middleware(['role:Administrador'])->prefix('companies')->group(function () {
        Route::get('/', \App\Livewire\Companies\CompanyIndex::class)->name('companies.index');
        Route::get('/create', \App\Livewire\Companies\CompanyBuilder::class)->name('companies.create');
        Route::get('/{id}/edit', \App\Livewire\Companies\CompanyBuilder::class)->name('companies.edit');
        Route::get('/{empresa}/locations', \App\Livewire\Companies\LocationManager::class)->name('companies.locations');
    });
    
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| Rutas de Angular (catch-all para el frontend existente)
|--------------------------------------------------------------------------
*/
Route::any('/', [AngularController::class, 'index']);
Route::any('/{any}', [AngularController::class, 'panel'])->where('any', '^(?!api|checklists|inspections).*$');