<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\InspectionPDFController;
use App\Livewire\Checklists\Dashboard;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Technician\TechnicianDashboard;
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
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', Login::class)->name('login');
Route::get('/constancia/firmar/{token}', \App\Livewire\Public\PublicSignature::class)->name('constancia.firmar');

/*
|--------------------------------------------------------------------------
| Rutas de Checklists Dinámicos (Protegidas)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Redirect condicional al dashboard correcto
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isAdmin()) {
            return redirect()->route('gestor.dashboard');
        }
        return redirect()->route('tecnico.dashboard');
    })->name('dashboard');
    
    /*
    |--------------------------------------------------------------------------
    | Rutas GESTOR (Prefijo: /gestor)
    |--------------------------------------------------------------------------
    */
    Route::prefix('gestor')->name('gestor.')->middleware(['role:Administrador'])->group(function() {
        // Dashboard
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

        // Visitas
        Route::prefix('visits')->name('visits.')->group(function () {
            Route::get('/history', \App\Livewire\Visits\VisitHistory::class)->name('history');
            Route::get('/in-progress/{visit}', \App\Livewire\Visits\VisitInProgress::class)->name('in-progress');
            Route::get('/{id}/certificate', [\App\Http\Controllers\VisitCertificatePDFController::class, 'preview'])->name('certificate.preview');
            Route::get('/{id}/certificate/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'download'])->name('certificate.download');
            Route::get('/checklist/{id}', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistPreview'])->name('checklist.preview');
            Route::get('/checklist/{id}/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistDownload'])->name('checklist.download');
        });

        // Administración
        Route::get('/administracion', \App\Livewire\Admin\AdministrationHub::class)->name('administration.hub');
        
        Route::prefix('users')->group(function () {
            Route::get('/', \App\Livewire\Users\UserManager::class)->name('users.index');
        });
        
        Route::prefix('tecnicos')->group(function () {
            Route::get('/', \App\Livewire\Users\TechnicianManager::class)->name('technicians.index');
        });

        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', \App\Livewire\Companies\CompanyIndex::class)->name('index');
            Route::get('/create', \App\Livewire\Companies\CompanyBuilder::class)->name('create');
            Route::get('/{id}/edit', \App\Livewire\Companies\CompanyBuilder::class)->name('edit');
            Route::get('/{empresa}/locations', \App\Livewire\Companies\LocationManager::class)->name('locations');
        });

        Route::prefix('assignments')->name('assignments.')->group(function () {
            Route::get('/', \App\Livewire\Assignments\AssignmentManager::class)->name('index');
        });
        


        // Hallazgos
        Route::prefix('hallazgos')->name('hallazgos.')->group(function () {
            Route::get('/', \App\Livewire\Hallazgos\HallazgoManager::class)->name('index');
            Route::get('/crear', \App\Livewire\Hallazgos\HallazgoWizard::class)->name('create');
            Route::get('/{id}/editar', \App\Livewire\Hallazgos\HallazgoWizard::class)->name('edit');
            Route::get('/{id}', \App\Livewire\Hallazgos\HallazgoDetail::class)->name('show');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'download'])->name('pdf');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Incidentes
        Route::prefix('incidentes')->name('incidentes.')->group(function () {
            Route::get('/', \App\Livewire\Incidentes\IncidenteManager::class)->name('index');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Incidentes\IncidentePDFController::class, 'download'])->name('pdf');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\Incidentes\IncidentePDFController::class, 'preview'])->name('pdf.preview');
        });

        // Historial Inspecciones
        Route::prefix('inspections')->name('inspections.')->group(function () {
            Route::get('/', \App\Livewire\Checklists\InspectionHistory::class)->name('index');
            Route::get('/{id}', \App\Livewire\Checklists\InspectionShow::class)->name('show');
            Route::get('/{id}/pdf', [InspectionPDFController::class, 'download'])->name('pdf.download');
            Route::get('/{id}/pdf/preview', [InspectionPDFController::class, 'preview'])->name('pdf.preview');
            // Route::get('/{id}/delete', ...)->name('delete'); // Pendiente funcionalidad
        });

        // Checklists
        Route::prefix('checklists')->name('checklists.')->group(function () {
            Route::get('/', ChecklistIndex::class)->name('index');
            Route::get('/create', ChecklistBuilder::class)->name('create');
            Route::get('/{id}/edit', ChecklistBuilder::class)->name('edit');
            Route::get('/{checklist}/emular', \App\Livewire\Checklists\ChecklistEmulator::class)->name('emulator');
            Route::get('/{id}/fill/{assignment?}', \App\Livewire\Technician\InspectionWizard::class)->name('fill');
            Route::get('/{id}', ChecklistBuilder::class)->name('show'); // Añadida ruta show si es necesario
        });

            // Rutas de Categorías
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', CategoryIndex::class)->name('index');
            Route::get('/create', CategoryBuilderComponent::class)->name('create');
            Route::get('/{id}/edit', CategoryBuilderComponent::class)->name('edit');
            Route::get('/{category}/subcategories', SubcategoryManager::class)->name('subcategories');
        });

        // Capacitaciones
        Route::prefix('trainings')->name('trainings.')->group(function () {
            Route::get('/', \App\Livewire\Trainings\TrainingIndex::class)->name('index');
            Route::get('/create', \App\Livewire\Trainings\TrainingBuilder::class)->name('create');
            Route::get('/{id}/edit', \App\Livewire\Trainings\TrainingBuilder::class)->name('edit');
            Route::get('/{id}/attendance', \App\Livewire\Trainings\AttendanceTracker::class)->name('attendance');
            Route::get('/{id}/pdf', [\App\Http\Controllers\TrainingPDFController::class, 'download'])->name('pdf.download');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\TrainingPDFController::class, 'preview'])->name('pdf.preview');
            // Delete route if needed
        });

        // Ejecuciones Avanzadas (solo Contrato Anual)
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/contrato-anual', \App\Livewire\Contracts\AnnualContractDashboard::class)->name('annual-contract.index');
            Route::get('/contrato-anual/pdf', [\App\Http\Controllers\Contracts\AnnualContractReportController::class, 'export'])->name('annual-contract.pdf');
        });

        // Informe Maestro (Informe de la Muerte)
        Route::prefix('master-reports')->name('master-reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\MasterReportController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\MasterReportController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\MasterReportController::class, 'store'])->name('store');
            Route::get('/{id}/pdf', [\App\Http\Controllers\MasterReportController::class, 'preview'])->name('pdf');
        });

        // Personalización de Empresa
        Route::get('/administration/branding/{id}', \App\Livewire\Companies\CompanyBranding::class)->name('administration.branding');

    });

    /*
    |--------------------------------------------------------------------------
    | Rutas TÉCNICO (Prefijo: /tecnico)
    |--------------------------------------------------------------------------
    */
    Route::prefix('tecnico')->name('tecnico.')->middleware(['role:Técnico,tecnico'])->group(function() {
        // Dashboard
        Route::get('/dashboard', TechnicianDashboard::class)->name('dashboard');

        // Checklists (Asignaciones y Llenado)
        Route::prefix('checklists')->name('checklists.')->group(function () {
             Route::get('/my-assignments', \App\Livewire\Technician\MyAssignments::class)->name('tasks.index'); // Mis Tareas / Inspecciones
             Route::get('/{id}/fill/{assignment?}', \App\Livewire\Technician\InspectionWizard::class)->name('fill');
        });

        // Visitas
        Route::prefix('visits')->name('visits.')->group(function () {
            Route::get('/', \App\Livewire\Visits\VisitManager::class)->name('index'); // Mis Visitas
            Route::get('/in-progress/{visit}', \App\Livewire\Visits\VisitInProgress::class)->name('in-progress');
            Route::get('/{id}/certificate', [\App\Http\Controllers\VisitCertificatePDFController::class, 'preview'])->name('certificate.preview');
            Route::get('/{id}/certificate/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'download'])->name('certificate.download');
            Route::get('/checklist/{id}', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistPreview'])->name('checklist.preview');
            Route::get('/checklist/{id}/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistDownload'])->name('checklist.download');
        });

        // Capacitaciones
        Route::prefix('trainings')->name('trainings.')->group(function () {
            Route::get('/', \App\Livewire\Trainings\TrainingIndex::class)->name('index');
            Route::get('/create', \App\Livewire\Trainings\TrainingBuilder::class)->name('create');
            Route::get('/{id}/attendance', \App\Livewire\Trainings\AttendanceTracker::class)->name('attendance');
            Route::get('/{id}/pdf', [\App\Http\Controllers\TrainingPDFController::class, 'download'])->name('pdf.download');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\TrainingPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Ejecuciones Avanzadas (solo Contrato Anual)
        Route::get('/contrato-anual', \App\Livewire\Contracts\AnnualContractDashboard::class)->name('annual-contract.index');
        Route::get('/contrato-anual/pdf', [\App\Http\Controllers\Contracts\AnnualContractReportController::class, 'export'])->name('annual-contract.pdf');



        // Mis Hallazgos
        Route::get('/mis-hallazgos', \App\Livewire\Hallazgos\MisHallazgos::class)->name('hallazgos.index');
        
        // Hallazgos (Crear/Ver) - Nota: Crear principal es via Modal en visitas, pero mantenemos wizard si existe
        Route::prefix('hallazgos')->name('hallazgos.')->group(function () {
             Route::get('/crear', \App\Livewire\Hallazgos\HallazgoWizard::class)->name('create');
             Route::get('/{id}', \App\Livewire\Hallazgos\HallazgoDetail::class)->name('show');
             Route::get('/{id}/pdf', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'download'])->name('pdf');
             Route::get('/{id}/pdf/preview', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Historial Inspecciones
        Route::prefix('inspections')->name('inspections.')->group(function () {
            Route::get('/', \App\Livewire\Checklists\InspectionHistory::class)->name('index');
            Route::get('/{id}', \App\Livewire\Checklists\InspectionShow::class)->name('show');
            Route::get('/{id}/pdf', [InspectionPDFController::class, 'download'])->name('pdf.download');
            Route::get('/{id}/pdf/preview', [InspectionPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Mis Incidentes
        Route::get('/incidentes', \App\Livewire\Incidentes\IncidenteManager::class)->name('incidentes.index');
        Route::get('/incidentes/{id}/pdf', [\App\Http\Controllers\Incidentes\IncidentePDFController::class, 'download'])->name('incidentes.pdf');
        Route::get('/incidentes/{id}/pdf/preview', [\App\Http\Controllers\Incidentes\IncidentePDFController::class, 'preview'])->name('incidentes.pdf.preview');

        // Informe Maestro (Redacción)
        Route::prefix('master-reports')->name('master-reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\MasterReportController::class, 'index'])->name('index');
            Route::get('/{id}/edit', [\App\Http\Controllers\MasterReportController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\MasterReportController::class, 'update'])->name('update');
            Route::get('/{id}/pdf', [\App\Http\Controllers\MasterReportController::class, 'preview'])->name('pdf');
        });

    });

    /*
    |--------------------------------------------------------------------------
    | Rutas SUPER ADMIN (Prefijo: /admin)
    |--------------------------------------------------------------------------
    | Reservado para la gestión global de consultoras y monitoreo del ecosistema.
    */
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin'])->group(function() {
        Route::get('/dashboard', \App\Livewire\Admin\EcosystemDashboard::class)->name('dashboard');
        Route::get('/consultants', \App\Livewire\Admin\ConsultantManager::class)->name('consultants');
        Route::get('/consultants/{consultant}/edit', \App\Livewire\Admin\ConsultantEdit::class)->name('consultants.edit');
        Route::get('/consultants/{consultant}/users', \App\Livewire\Admin\ConsultantUsers::class)->name('consultants.users');
    });

    // Logout
    // Logout
    Route::match(['get', 'post'], '/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
