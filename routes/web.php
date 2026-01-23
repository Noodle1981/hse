<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AngularController;
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
        if (auth()->user()->isAdmin()) {
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
        
        /*
        Route::prefix('projects')->name('projects.')->group(function () {
            Route::get('/', \App\Livewire\Companies\ProjectManager::class)->name('index'); // Si existiera
        });
        */

        // Gestión EPP
         Route::prefix('epp')->name('epp.')->group(function () {
            Route::get('/catalog', \App\Livewire\Epp\EppCatalog::class)->name('catalog');
            Route::get('/import-employees', \App\Livewire\Epp\EmployeeImporter::class)->name('employees.import');
            Route::get('/employees', \App\Livewire\Epp\EmployeeManager::class)->name('employees.index');
            Route::get('/history', \App\Livewire\Epp\EppHistory::class)->name('history');
            Route::get('/delivery/{id}/pdf', [\App\Http\Controllers\EppPDFController::class, 'download'])->name('delivery.pdf');
            Route::get('/delivery/{id}/pdf/preview', [\App\Http\Controllers\EppPDFController::class, 'preview'])->name('delivery.pdf.preview');
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
             // Agregar rutas de detalle/PDF si existen
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

        // Ejecuciones Avanzadas
        Route::middleware(['role:Administrador'])->group(function () {
             Route::get('/risk-matrices/ejecuciones-avanzadas', \App\Livewire\Risks\AdvancedExecutionsHub::class)->name('advanced-executions.hub');
        });

        Route::prefix('risk-matrices')->name('risk-matrices.')->group(function () {
            Route::get('/', \App\Livewire\Risks\RiskMatrixManager::class)->name('index');
            Route::get('/dashboard-riesgos', \App\Livewire\Risks\RiskDashboard::class)->name('dashboard');
            Route::get('/{matrixId}/editor', \App\Livewire\Risks\RiskMatrixEditor::class)->name('editor');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Risks\RiskMatrixReportController::class, 'export'])->name('pdf');
            // Contrato Anual
            Route::get('/contrato-anual', \App\Livewire\Risks\AnnualContractDashboard::class)->name('annual-contract.index');
        });
        
        // Contrato Anual PDF - fuera del grupo risk-matrices pero con nombre consistente?
        Route::get('/contrato-anual/pdf', [\App\Http\Controllers\Risks\AnnualContractReportController::class, 'export'])->name('annual-contract.pdf');

    });

    /*
    |--------------------------------------------------------------------------
    | Rutas TÉCNICO (Prefijo: /tecnico)
    |--------------------------------------------------------------------------
    */
    Route::prefix('tecnico')->name('tecnico.')->middleware(['role:Técnico'])->group(function() {
        // Dashboard
        Route::get('/dashboard', TechnicianDashboard::class)->name('dashboard');

        // Checklists (Asignaciones y Llenado)
        Route::prefix('checklists')->name('checklists.')->group(function () {
             Route::get('/my-assignments', \App\Livewire\Technician\MyAssignments::class)->name('tasks.index'); // Mis Tareas
             Route::get('/{id}/fill', \App\Livewire\Technician\InspectionWizard::class)->name('fill');
        });

        // Visitas
        Route::prefix('visits')->name('visits.')->group(function () {
            Route::get('/', \App\Livewire\Visits\VisitManager::class)->name('index'); // Mis Visitas
            Route::get('/in-progress/{visit}', \App\Livewire\Visits\VisitInProgress::class)->name('in-progress');
        });

        // Capacitaciones
        Route::prefix('trainings')->name('trainings.')->group(function () {
            Route::get('/create', \App\Livewire\Trainings\TrainingBuilder::class)->name('create');
            Route::get('/{id}/attendance', \App\Livewire\Trainings\AttendanceTracker::class)->name('attendance');
            Route::get('/{id}/pdf', [\App\Http\Controllers\TrainingPDFController::class, 'download'])->name('pdf.download');
        });

        // Ejecuciones Avanzadas
        Route::get('/risk-matrices/ejecuciones-avanzadas', \App\Livewire\Risks\AdvancedExecutionsHub::class)->name('advanced-executions.hub');

        Route::prefix('risk-matrices')->name('risk-matrices.')->group(function () {
            Route::get('/', \App\Livewire\Risks\RiskMatrixManager::class)->name('index'); // Evaluar
            Route::get('/dashboard-riesgos', \App\Livewire\Risks\RiskDashboard::class)->name('dashboard');
            Route::get('/{matrixId}/evaluate', \App\Livewire\Risks\RiskEvaluator::class)->name('evaluator');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Risks\RiskMatrixReportController::class, 'export'])->name('pdf');
            Route::get('/contrato-anual', \App\Livewire\Risks\AnnualContractDashboard::class)->name('annual-contract.index');
        });

        // Gestión EPP
        Route::prefix('epp')->name('epp.')->group(function () {
            Route::get('/deliver', \App\Livewire\Epp\EppDeliveryWizard::class)->name('deliver');
            Route::get('/history', \App\Livewire\Epp\EppHistory::class)->name('history');
            Route::get('/employees', \App\Livewire\Epp\EmployeeManager::class)->name('employees.index'); // Solo lectura?
            Route::get('/delivery/{id}/pdf', [\App\Http\Controllers\EppPDFController::class, 'download'])->name('delivery.pdf');
            Route::get('/delivery/{id}/pdf/preview', [\App\Http\Controllers\EppPDFController::class, 'preview'])->name('delivery.pdf.preview');
        });

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
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas LEGACY (Mantener temporalmente para compatibilidad)
    |--------------------------------------------------------------------------
    */
    
    // Dashboards específicos (Legacy)
    Route::get('/admin/dashboard', AdminDashboard::class)
        ->middleware(['role:Administrador'])
        ->name('admin.dashboard');
    
    Route::get('/technician/dashboard', TechnicianDashboard::class)
        ->middleware(['role:Técnico,Administrador'])
        ->name('technician.dashboard');
    
    Route::prefix('checklists')->group(function () {
        // Solo Admin puede ver listado, crear/editar
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/', ChecklistIndex::class)->name('checklists.index');
            Route::get('/create', ChecklistBuilder::class)->name('checklists.create');
            Route::get('/{id}/edit', ChecklistBuilder::class)->name('checklists.edit');
        });
        
        // Admin y Técnico pueden llenar y ver sus tareas
        Route::middleware(['role:Administrador,Técnico'])->group(function () {
            Route::get('/my-assignments', \App\Livewire\Technician\MyAssignments::class)->name('tasks.index');
            Route::get('/{id}/fill', \App\Livewire\Technician\InspectionWizard::class)->name('checklists.fill');
        });
    });

    Route::prefix('inspections')->group(function () {
        Route::get('/', InspectionHistory::class)->name('inspections.index');
        Route::get('/{id}', InspectionShow::class)->name('inspections.show');
        Route::get('/{id}/pdf', [InspectionPDFController::class, 'download'])->name('inspections.pdf.download');
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

    // Rutas de Asignaciones (Solo Admin)
    Route::middleware(['role:Administrador'])->prefix('assignments')->group(function () {
        Route::get('/', \App\Livewire\Assignments\AssignmentManager::class)->name('assignments.index');
    });

    // Hub de Administración (Solo Admin)
    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/administracion', \App\Livewire\Admin\AdministrationHub::class)->name('administration.hub');
    });

    // Rutas de Usuarios (Solo Admin)
    Route::middleware(['role:Administrador'])->prefix('users')->group(function () {
        Route::get('/', \App\Livewire\Users\UserManager::class)->name('users.index');
    });

    // Rutas de Técnicos (Solo Admin) - Nueva ruta para gestión
    Route::middleware(['role:Administrador'])->prefix('tecnicos')->group(function () {
        Route::get('/', \App\Livewire\Users\TechnicianManager::class)->name('technicians.index');
    });

    // Rutas de Capacitaciones
    Route::prefix('trainings')->group(function () {
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/', \App\Livewire\Trainings\TrainingIndex::class)->name('trainings.index');
        });
        
        // Admin y Técnico pueden crear, editar y tomar asistencia
        Route::middleware(['role:Administrador,Técnico'])->group(function () {
            Route::get('/create', \App\Livewire\Trainings\TrainingBuilder::class)->name('trainings.create');
            Route::get('/{id}/edit', \App\Livewire\Trainings\TrainingBuilder::class)->name('trainings.edit');
            Route::get('/{id}/attendance', \App\Livewire\Trainings\AttendanceTracker::class)->name('trainings.attendance');
            Route::get('/{id}/pdf', [\App\Http\Controllers\TrainingPDFController::class, 'download'])->name('trainings.pdf.download');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\TrainingPDFController::class, 'preview'])->name('trainings.pdf.preview');
        });
    });

    // Rutas de EPP y Empleados (Sprint 9)
    Route::prefix('epp')->group(function () {
        // Solo Admin
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/catalog', \App\Livewire\Epp\EppCatalog::class)->name('epp.catalog');
            Route::get('/import-employees', \App\Livewire\Epp\EmployeeImporter::class)->name('epp.employees.import');
        });

        // Admin y Técnico (Entrega de Campo y Solicitudes)
        Route::middleware(['role:Administrador,Técnico'])->group(function () {
             Route::get('/deliver', \App\Livewire\Epp\EppDeliveryWizard::class)->name('epp.deliver');
        });

        // Admin y Técnico (Personal e Historial)
        Route::middleware(['role:Administrador,Técnico'])->group(function () {
            Route::get('/employees', \App\Livewire\Epp\EmployeeManager::class)->name('epp.employees.index');
            Route::get('/history', \App\Livewire\Epp\EppHistory::class)->name('epp.history');
            Route::get('/delivery/{id}/pdf', [\App\Http\Controllers\EppPDFController::class, 'download'])->name('epp.delivery.pdf');
            Route::get('/delivery/{id}/pdf/preview', [\App\Http\Controllers\EppPDFController::class, 'preview'])->name('epp.delivery.pdf.preview');
        });
    });

    // Rutas de Matriz de Riesgo (Sprint 10)
    Route::prefix('risk-matrices')->group(function () {
        // Hub de Ejecuciones Avanzadas (Admin y Técnico)
        Route::middleware(['role:Administrador,Técnico'])->group(function () {
            Route::get('/ejecuciones-avanzadas', \App\Livewire\Risks\AdvancedExecutionsHub::class)->name('advanced-executions.hub');
        });
        
        // Solo Admin puede diseñar las matrices
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/{matrixId}/editor', \App\Livewire\Risks\RiskMatrixEditor::class)->name('risk-matrices.editor');
        });

        // Admin y Técnico pueden ver listado, evaluar y ver dashboard
        Route::middleware(['role:Administrador,Técnico'])->group(function () {
            Route::get('/', \App\Livewire\Risks\RiskMatrixManager::class)->name('risk-matrices.index');
            Route::get('/dashboard-riesgos', \App\Livewire\Risks\RiskDashboard::class)->name('risk-matrices.dashboard');
            Route::get('/{matrixId}/evaluate', \App\Livewire\Risks\RiskEvaluator::class)->name('risk-matrices.evaluator');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Risks\RiskMatrixReportController::class, 'export'])->name('risk-matrices.pdf');
            Route::get('/contrato-anual', \App\Livewire\Risks\AnnualContractDashboard::class)->name('annual-contract.index');
        });
    });
    
    Route::get('/contrato-anual/pdf', [\App\Http\Controllers\Risks\AnnualContractReportController::class, 'export'])->name('annual-contract.pdf');

    // Rutas de Hallazgos (Fase 3 - Módulo de Hallazgos)
    Route::prefix('hallazgos')->name('hallazgos.')->group(function () {
        // Admin y Administrativos - Gestión completa
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/', \App\Livewire\Hallazgos\HallazgoManager::class)->name('index');
            Route::get('/{id}/editar', \App\Livewire\Hallazgos\HallazgoWizard::class)->name('edit');
        });
        
        // Admin, Administrativos y Técnicos - Crear, ver y PDF
        Route::middleware(['role:Administrador,Técnico'])->group(function () {
            Route::get('/crear', \App\Livewire\Hallazgos\HallazgoWizard::class)->name('create');
            Route::get('/{id}', \App\Livewire\Hallazgos\HallazgoDetail::class)->name('show');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'download'])->name('pdf');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'preview'])->name('pdf.preview');
        });
    });

    // Mis Hallazgos (Técnicos)
    Route::get('/mis-hallazgos', \App\Livewire\Hallazgos\MisHallazgos::class)
        ->middleware(['role:Técnico,Administrador'])
        ->name('mis-hallazgos');

    // Rutas de Incidentes (Fase 3 - Módulo de Hallazgos)
    Route::prefix('incidentes')->name('incidentes.')->group(function () {
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/', \App\Livewire\Incidentes\IncidenteManager::class)->name('index');
        });
    });

    // Rutas de Visitas (Sistema Integrado)
    Route::prefix('visits')->name('visits.')->group(function () {
        // Rutas accesibles por Técnicos y Administradores
        Route::middleware(['role:Técnico,Administrador'])->group(function () {
            // Index de visitas (Cada uno ve lo que le corresponde según lógica del componente)
            Route::get('/', \App\Livewire\Visits\VisitManager::class)->name('index');
            
            // Visita en progreso
            Route::get('/in-progress/{visit}', \App\Livewire\Visits\VisitInProgress::class)->name('in-progress');
            
            // PDF de constancia
            Route::get('/{visit}/certificate/preview', [\App\Http\Controllers\VisitCertificatePDFController::class, 'preview'])->name('certificate.preview');
            Route::get('/{visit}/certificate/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'download'])->name('certificate.download');
        });
        
        // Historial solo Admin (o Técnico también?) - Inicialmente Admin
        Route::middleware(['role:Administrador'])->group(function () {
            Route::get('/history', \App\Livewire\Visits\VisitHistory::class)->name('history');
        });
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