<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionPDFController;
use App\Http\Controllers\MasterReportController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SignatureApprovalController;
use App\Http\Controllers\TrainingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::get('/constancia/firmar/{token}', function () {
    // TODO: Migrar PublicSignature a React
    abort(404, 'Vista de firma pública pendiente de migración a React.');
})->name('constancia.firmar');

/*
|--------------------------------------------------------------------------
| Rutas de Checklists Dinámicos (Protegidas)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Redirect condicional al dashboard correcto
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user->isGlobalUser()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isTechnician()) {
            return redirect()->route('tecnico.checklists.tasks.index');
        }

        // Redireccionar al Dashboard de Gestor por defecto para Administradores/Otros
        return redirect()->route('gestor.dashboard');
    })->name('dashboard');

    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile');
    Route::post('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/perfil/signature', [ProfileController::class, 'updateSignature'])->name('profile.signature');
    Route::post('/perfil/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    /*
    |--------------------------------------------------------------------------
    | Rutas GESTOR (Prefijo: /gestor)
    |--------------------------------------------------------------------------
    */
    Route::prefix('gestor')->name('gestor.')->middleware(['role:Administrador'])->group(function () {
        // Dashboard del Gestor (principal)
        Route::get('/dashboard', [AdministrationController::class, 'gestorDashboard'])->name('dashboard');

        // Visitas
        Route::prefix('visits')->name('visits.')->group(function () {
            Route::get('/{id}/certificate', [\App\Http\Controllers\VisitCertificatePDFController::class, 'preview'])->name('certificate.preview');
            Route::get('/{id}/certificate/loading', [\App\Http\Controllers\VisitCertificatePDFController::class, 'loading'])->name('certificate.loading');
            Route::get('/{id}/certificate/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'download'])->name('certificate.download');
            Route::get('/checklist/{id}', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistPreview'])->name('checklist.preview');
            Route::get('/checklist/{id}/loading', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistLoading'])->name('checklist.loading');
            Route::get('/checklist/{id}/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistDownload'])->name('checklist.download');
        });

        // Administración
        Route::get('/administracion', [AdministrationController::class, 'hub'])->name('administration.hub');

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdministrationController::class, 'dashboard'])->name('dashboard');
            Route::get('/hub', [AdministrationController::class, 'hub'])->name('hub');
        });

        Route::prefix('users')->group(function () {
            Route::get('/', [AdministrationController::class, 'users'])->name('users.index');
            Route::post('/save', [AdministrationController::class, 'saveUser'])->name('users.save');
            Route::delete('/{user}', [AdministrationController::class, 'deleteUser'])->name('users.delete');
            Route::post('/{id}/restore', [AdministrationController::class, 'restoreUser'])->name('users.restore');
            Route::get('/pending-signatures', [SignatureApprovalController::class, 'pending'])->name('signatures.pending');
            Route::post('/{user}/approve-signature', [SignatureApprovalController::class, 'approve'])->name('signatures.approve');
            Route::post('/{user}/reject-signature', [SignatureApprovalController::class, 'reject'])->name('signatures.reject');
        });

        Route::prefix('tecnicos')->group(function () {
            Route::get('/', [AdministrationController::class, 'technicians'])->name('technicians.index');
            Route::post('/{user}/assign', [AdministrationController::class, 'assignSede'])->name('technicians.assign');
            Route::post('/{user}/remove', [AdministrationController::class, 'removeAssignment'])->name('technicians.remove');
        });

        // API para componentes React (Administración)
        Route::prefix('api')->group(function () {
            Route::get('/companies/{empresa}/sites', [AdministrationController::class, 'getCompanySites']);
            Route::get('/technicians/{user}/assignments', [AdministrationController::class, 'getTechnicianAssignments']);
        });

        Route::prefix('companies')->name('companies.')->group(function () {
            Route::get('/', [CompanyController::class, 'index'])->name('index');
            Route::get('/create', [CompanyController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [CompanyController::class, 'edit'])->name('edit');
            Route::post('/save', [CompanyController::class, 'save'])->name('save');
            Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('delete');
            Route::post('/{id}/restore', [CompanyController::class, 'restore'])->name('restore');

            // Sedes (Locations)
            Route::prefix('{empresa}/sedes')->name('locations')->group(function () {
                Route::get('/', [CompanyController::class, 'locations']);
                Route::post('/save', [CompanyController::class, 'saveLocation'])->name('.save');
            });
        });

        Route::prefix('sedes')->name('companies.locations.')->group(function () {
            Route::delete('/{sede}', [CompanyController::class, 'deleteLocation'])->name('delete');
            Route::post('/{id}/restore', [CompanyController::class, 'restoreLocation'])->name('restore');
            Route::post('/{sede}/toggle-user', [CompanyController::class, 'toggleUserAssignment'])->name('toggle-user');
        });

        Route::prefix('assignments')->name('assignments.')->group(function () {
            // TODO: Migrar AssignmentManager a React
        });

        // Hallazgos (React)
        Route::prefix('hallazgos')->name('hallazgos.')->group(function () {
            Route::get('/', [FindingController::class, 'index'])->name('index');
            Route::get('/reportar', [FindingController::class, 'create'])->name('create');
            Route::post('/', [FindingController::class, 'store'])->name('store');
            Route::get('/{finding}', [FindingController::class, 'show'])->name('show');
            Route::post('/{finding}/update', [FindingController::class, 'addUpdate'])->name('update');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'download'])->name('pdf');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Incidentes (React)
        Route::prefix('incidentes')->name('incidentes.')->group(function () {
            Route::get('/', [IncidenteController::class, 'index'])->name('index');
            Route::get('/{incident}', [IncidenteController::class, 'show'])->name('show');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Incidentes\IncidentePDFController::class, 'download'])->name('pdf');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\Incidentes\IncidentePDFController::class, 'preview'])->name('pdf.preview');
        });

        // Historial Inspecciones (React)
        Route::prefix('inspections')->name('inspections.')->group(function () {
            Route::get('/', [InspectionController::class, 'historyGestor'])->name('index');
            Route::get('/{inspection}', [InspectionController::class, 'show'])->name('show');
            Route::get('/{id}/pdf', [InspectionPDFController::class, 'download'])->name('pdf.download');
            Route::get('/{id}/pdf/preview', [InspectionPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Operaciones (Visitas e Inspecciones)
        Route::prefix('operations')->name('operations.')->group(function () {
            Route::prefix('visits')->name('visits.')->group(function () {
                Route::get('/', [OperationController::class, 'index'])->middleware(['role:Administrador'])->name('index');
            });

            Route::prefix('trainings')->name('trainings.')->group(function () {
                Route::get('/', [TrainingController::class, 'index'])->name('index');
                Route::get('/create', [TrainingController::class, 'create'])->name('create');
                Route::post('/store', [TrainingController::class, 'store'])->name('store');
                Route::get('/session/{visitTraining}', [TrainingController::class, 'session'])->name('session');
                Route::post('/start-in-visit/{visit}', [TrainingController::class, 'startInVisit'])->name('start-in-visit');
                Route::post('/add-attendee/{visitTraining}', [TrainingController::class, 'addAttendee'])->name('add-attendee');
                Route::post('/remove-attendee/{visitTraining}/{index}', [TrainingController::class, 'removeAttendee'])->name('remove-attendee');
                Route::post('/session/{visitTraining}/update-details', [TrainingController::class, 'updateDetails'])->name('update-details');
                Route::post('/session/{visitTraining}/finalize', [TrainingController::class, 'finalize'])->name('finalize');
            });

        });

        // Checklists
        Route::prefix('checklists')->name('checklists.')->group(function () {
            Route::get('/', [ChecklistController::class, 'index'])->name('index');
            Route::get('/create', [ChecklistController::class, 'create'])->name('create');
            Route::get('/{id}/edit', [ChecklistController::class, 'edit'])->name('edit');
            Route::post('/save', [ChecklistController::class, 'save'])->name('save');
            Route::post('/{checklist}/toggle', [ChecklistController::class, 'toggleStatus'])->name('toggle');
            Route::post('/{original}/duplicate', [ChecklistController::class, 'duplicate'])->name('duplicate');

            Route::get('/{checklist}/emular', [ChecklistController::class, 'emulate'])->name('emulator');
            Route::get('/{id}/wizard', [InspectionController::class, 'wizard'])->name('fill');
            Route::post('/{id}/wizard/save/{inspection}', [InspectionController::class, 'saveProgress'])->name('wizard.save');
            Route::post('/{id}/wizard/complete/{inspection}', [InspectionController::class, 'complete'])->name('wizard.complete');
        });

        // Rutas de Categorías
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::post('/save', [CategoryController::class, 'save'])->name('save');
            Route::get('/{category}/subcategories', [CategoryController::class, 'subcategories'])->name('subcategories');
            Route::post('/{category}/subcategories/save', [CategoryController::class, 'saveSubcategory'])->name('subcategories.save');
            Route::post('/toggle/{type}/{id}', [CategoryController::class, 'toggleStatus'])->name('toggle');
        });

        // Capacitaciones (React)
        Route::prefix('trainings')->name('trainings.')->group(function () {
            Route::get('/', [TrainingController::class, 'index'])->name('index');
            Route::get('/create', [TrainingController::class, 'create'])->name('create');
            Route::post('/', [TrainingController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [TrainingController::class, 'edit'])->name('edit');
            Route::get('/{id}/attendance', [TrainingController::class, 'attendance'])->name('attendance');
            Route::get('/{id}/pdf', [\App\Http\Controllers\TrainingPDFController::class, 'download'])->name('pdf.download');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\TrainingPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Personalización de Empresa (TODO: Migrar a React)
        // Route::get('/administration/branding/{id}', ...)->name('administration.branding');

    });

    /*
    |--------------------------------------------------------------------------
    | Rutas GESTOR Compartidas con Técnico (Prefijo: /gestor)
    |--------------------------------------------------------------------------
    */
    Route::prefix('gestor')->name('gestor.')->middleware(['role:Administrador,Gestor,tecnico,Técnico,Socio'])->group(function () {
        Route::prefix('operations')->name('operations.')->group(function () {
            Route::prefix('visits')->name('visits.')->group(function () {
                Route::get('/create', [OperationController::class, 'create'])->name('create');
                Route::post('/store', [OperationController::class, 'store'])->name('store');
                Route::get('/{visit}', [OperationController::class, 'show'])->name('show');
                Route::post('/{visit}/finalize', [OperationController::class, 'finalize'])->name('finalize');
            });
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas COMPARTIDAS (Gestor + Técnico)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:Administrador,Gestor,tecnico,Técnico,Socio'])->group(function () {

        // Contrato Anual / PPD
        Route::prefix('contrato-anual')->name('annual-contract.')->group(function () {
            Route::get('/', [ContractController::class, 'index'])->name('index');
            Route::post('/activity', [ContractController::class, 'store'])->name('store');
            Route::post('/activity/{id}/toggle', [ContractController::class, 'toggleMonth'])->name('toggle');
            Route::post('/activity/{id}/date', [ContractController::class, 'updateDate'])->name('date');
            Route::put('/activity/{id}', [ContractController::class, 'update'])->name('update');
            Route::delete('/activity/{id}', [ContractController::class, 'destroy'])->name('destroy');
            Route::get('/pdf', [\App\Http\Controllers\Contracts\AnnualContractReportController::class, 'export'])->name('pdf');
        });

        // Informe Maestro
        Route::prefix('master-reports')->name('master-reports.')->group(function () {
            Route::get('/', [MasterReportController::class, 'index'])->name('index');
            Route::get('/create', [MasterReportController::class, 'create'])->name('create');
            Route::post('/', [MasterReportController::class, 'store'])->name('store');
            Route::get('/{masterReport}', [MasterReportController::class, 'show'])->name('show');
            Route::get('/{masterReport}/edit', [MasterReportController::class, 'edit'])->name('edit');
            Route::put('/{masterReport}', [MasterReportController::class, 'update'])->name('update');
            Route::get('/{masterReport}/pdf', [MasterReportController::class, 'preview'])->name('pdf');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas TÉCNICO (Prefijo: /tecnico)
    |--------------------------------------------------------------------------
    */
    Route::prefix('tecnico')->name('tecnico.')->middleware(['role:Técnico,tecnico,gestor,Administrador,Socio'])->group(function () {

        // Checklists (Asignaciones y Llenado) (React)
        Route::prefix('checklists')->name('checklists.')->group(function () {
            Route::get('/my-assignments', [OperationController::class, 'myAssignments'])->name('tasks.index'); // Mis Tareas / Inspecciones
            Route::get('/{id}/fill/{assignment?}', [InspectionController::class, 'wizard'])->name('fill');
            Route::post('/execute', [OperationController::class, 'startQuickExecution'])->name('execute');
            Route::post('/{id}/wizard/save/{inspection}', [InspectionController::class, 'saveProgress'])->name('wizard.save');
            Route::post('/{id}/wizard/complete/{inspection}', [InspectionController::class, 'complete'])->name('wizard.complete');
        });

        // Visitas (React)
        Route::prefix('visits')->name('visits.')->group(function () {
            Route::get('/', [OperationController::class, 'myVisits'])->name('index'); // Mis Visitas (Same as Assignments for now or specialized)
            Route::get('/in-progress/{visit}', [OperationController::class, 'show'])->name('in-progress');
            Route::post('/in-progress/{visit}/finalize', [OperationController::class, 'finalize'])->name('finalize');
            Route::post('/in-progress/{visit}/assign-suggested', [OperationController::class, 'assignSuggestedTask'])->name('assign-suggested');
            Route::get('/{id}/certificate', [\App\Http\Controllers\VisitCertificatePDFController::class, 'preview'])->name('certificate.preview');
            Route::get('/{id}/certificate/loading', [\App\Http\Controllers\VisitCertificatePDFController::class, 'loading'])->name('certificate.loading');
            Route::get('/{id}/certificate/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'download'])->name('certificate.download');
            Route::get('/checklist/{id}', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistPreview'])->name('checklist.preview');
            Route::get('/checklist/{id}/loading', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistLoading'])->name('checklist.loading');
            Route::get('/checklist/{id}/download', [\App\Http\Controllers\VisitCertificatePDFController::class, 'checklistDownload'])->name('checklist.download');
        });

        // Capacitaciones (Técnico - React)
        Route::prefix('trainings')->name('trainings.')->group(function () {
            Route::get('/', [TrainingController::class, 'index'])->name('index');
            Route::get('/create', [TrainingController::class, 'create'])->name('create');
            Route::get('/{id}/attendance', [TrainingController::class, 'attendance'])->name('attendance');
            Route::get('/{id}/pdf', [\App\Http\Controllers\TrainingPDFController::class, 'download'])->name('pdf.download');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\TrainingPDFController::class, 'preview'])->name('pdf.preview');
            Route::get('/session/{visitTraining}', [TrainingController::class, 'session'])->name('session');
            Route::post('/add-attendee/{visitTraining}', [TrainingController::class, 'addAttendee'])->name('add-attendee');
            Route::post('/remove-attendee/{visitTraining}/{index}', [TrainingController::class, 'removeAttendee'])->name('remove-attendee');
            Route::post('/start-in-visit/{visit}', [TrainingController::class, 'startInVisit'])->name('start-in-visit');
            Route::post('/session/{visitTraining}/update-details', [TrainingController::class, 'updateDetails'])->name('update-details');
            Route::post('/session/{visitTraining}/finalize', [TrainingController::class, 'finalize'])->name('finalize');
        });

        // Hallazgos (Técnico - React)
        Route::prefix('hallazgos')->name('hallazgos.')->group(function () {
            Route::get('/', [FindingController::class, 'myFindings'])->name('index'); // Redundante con mis-hallazgos mas arriba pero seguro
            Route::get('/reportar', [FindingController::class, 'create'])->name('create');
            Route::post('/', [FindingController::class, 'store'])->name('store');
            Route::get('/{finding}', [FindingController::class, 'show'])->name('show');
            Route::post('/{finding}/update', [FindingController::class, 'addUpdate'])->name('update');
            Route::get('/{id}/pdf', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'download'])->name('pdf');
            Route::get('/{id}/pdf/preview', [\App\Http\Controllers\Hallazgos\HallazgoPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Historial Inspecciones (Técnico - React)
        Route::prefix('inspections')->name('inspections.')->group(function () {
            Route::get('/', [InspectionController::class, 'historyTecnico'])->name('index');
            Route::get('/{inspection}', [InspectionController::class, 'show'])->name('show');
            Route::get('/{id}/pdf', [InspectionPDFController::class, 'download'])->name('pdf.download');
            Route::get('/{id}/pdf/preview', [InspectionPDFController::class, 'preview'])->name('pdf.preview');
        });

        // Incidentes (React)
        Route::get('/incidentes', [IncidenteController::class, 'index'])->name('incidentes.index');
        Route::get('/incidentes/reportar', [IncidenteController::class, 'create'])->name('incidentes.create');
        Route::get('/incidentes/{incident}', [IncidenteController::class, 'show'])->name('incidentes.show');
        Route::post('/incidentes/store', [IncidenteController::class, 'store'])->name('incidentes.store');
        Route::get('/incidentes/{id}/pdf', [\App\Http\Controllers\Incidentes\IncidentePDFController::class, 'download'])->name('incidentes.pdf');
        Route::get('/incidentes/{id}/pdf/preview', [\App\Http\Controllers\Incidentes\IncidentePDFController::class, 'preview'])->name('incidentes.pdf.preview');

    });

    /*
    |--------------------------------------------------------------------------
    | Rutas SUPER ADMIN y VENDEDOR (Prefijo: /admin)
    |--------------------------------------------------------------------------
    | Reservado para la gestión global de consultoras y personal de soporte/ventas.
    */
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin,vendedor'])->group(function () {
        Route::get('/dashboard', [AdministrationController::class, 'dashboard'])->name('dashboard');
        
        // Consultoras Management (Lectura, creación y edición compartida)
        Route::prefix('consultants')->name('consultants.')->group(function () {
            Route::get('/', [AdministrationController::class, 'consultantsIndex'])->name('index');
            Route::get('/create', [AdministrationController::class, 'consultantsCreate'])->name('create');
            Route::post('/store', [AdministrationController::class, 'consultantsStore'])->name('store');
            Route::get('/{consultant}/edit', [AdministrationController::class, 'consultantsEdit'])->name('edit');
            Route::post('/{consultant}/update', [AdministrationController::class, 'consultantsUpdate'])->name('update');

            // Acciones destructivas y gestión de staff interno restringidas a super_admin
            Route::middleware(['role:super_admin'])->group(function () {
                Route::delete('/{consultant}', [AdministrationController::class, 'consultantsDestroy'])->name('destroy');
                
                // Staff/Users Management per Consultora
                Route::get('/{consultant}/users', [AdministrationController::class, 'consultantUsersIndex'])->name('users.index');
                Route::post('/{consultant}/users/save', [AdministrationController::class, 'consultantUsersSave'])->name('users.save');
                Route::delete('/{consultant}/users/{user}', [AdministrationController::class, 'consultantUsersDestroy'])->name('users.destroy');
            });
        });

        // CRUD de Personal Global (Restringido a super_admin)
        Route::middleware(['role:super_admin'])->group(function () {
            Route::get('/staff', [AdministrationController::class, 'globalStaffIndex'])->name('staff.index');
            Route::post('/staff/save', [AdministrationController::class, 'globalStaffSave'])->name('staff.save');
            Route::delete('/staff/{user}', [AdministrationController::class, 'globalStaffDestroy'])->name('staff.destroy');
        });
    });

    // Logout
    // Role Switch
    Route::get('/switch-role/{role}', [\App\Http\Controllers\SwitchRoleController::class, 'switch'])->name('switch-role');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
