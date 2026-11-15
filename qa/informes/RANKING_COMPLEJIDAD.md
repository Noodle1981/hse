# Ranking de complejidad — HSE SaaS (XamaSafe / EP-Back)

> Generado: 2026-11-15

## Vistas y Layouts Frontend (React + Inertia) (umbral 500 lín · crítico 800 lín)

Archivos: 92 · sobre umbral: **0** · críticos: **0**

| Líneas | Estado | Archivo | Acceso a datos |
|---:|---|---|---|
| 488 | 🔵 Moderado | `resources/js/Pages/Gestor/Findings/Index.jsx` | — |
| 477 | 🔵 Moderado | `resources/js/Pages/Gestor/Dashboard.jsx` | — |
| 477 | 🔵 Moderado | `resources/js/Pages/HelpCenter/Index.jsx` | — |
| 474 | 🔵 Moderado | `resources/js/Pages/Profile/Show.jsx` | — |
| 446 | 🔵 Moderado | `resources/js/Pages/Admin/Consultants/Users.jsx` | — |
| 434 | 🔵 Moderado | `resources/js/Pages/Gestor/Incidents/Index.jsx` | — |
| 430 | 🔵 Moderado | `resources/js/Pages/Admin/Staff/Index.jsx` | — |
| 423 | 🔵 Moderado | `resources/js/Pages/Tecnico/Inspections/Wizard.jsx` | — |
| 401 | 🔵 Moderado | `resources/js/Pages/Gestor/Inspections/Index.jsx` | — |
| 392 | 🔵 Moderado | `resources/js/Pages/Tecnico/Visits/ActionSelector.jsx` | — |
| 390 | 🔵 Moderado | `resources/js/Pages/Gestor/Trainings/Index.jsx` | — |
| 388 | 🔵 Moderado | `resources/js/Pages/Tecnico/Trainings/Session.jsx` | — |
| 385 | 🔵 Moderado | `resources/js/Pages/Gestor/Users.jsx` | — |
| 379 | 🔵 Moderado | `resources/js/Pages/Tecnico/Visits/InProgress.jsx` | — |
| 376 | 🔵 Moderado | `resources/js/Pages/Gestor/Checklists/Builder.jsx` | — |
| 375 | 🔵 Moderado | `resources/js/Pages/Tecnico/Inspections/History.jsx` | — |
| 375 | 🔵 Moderado | `resources/js/Pages/Tecnico/Visits/History.jsx` | — |
| 359 | 🔵 Moderado | `resources/js/Pages/Gestor/Technicians.jsx` | — |
| 356 | 🔵 Moderado | `resources/js/Pages/Tecnico/Findings/Create.jsx` | — |
| 355 | 🔵 Moderado | `resources/js/Layouts/AuthenticatedLayout.jsx` | — |
| 343 | 🔵 Moderado | `resources/js/Pages/Admin/Dashboard.jsx` | — |
| 337 | 🔵 Moderado | `resources/js/Pages/Gestor/Assets/Edit.jsx` | — |
| 323 | 🔵 Moderado | `resources/js/Pages/Gestor/Checklists/Index.jsx` | — |
| 318 | 🔵 Moderado | `resources/js/Pages/Tecnico/Incidents/Create.jsx` | — |
| 307 | 🔵 Moderado | `resources/js/Pages/Gestor/Assets/Create.jsx` | — |
| 302 | 🔵 Moderado | `resources/js/Pages/Tecnico/Partials/QuickExecutionModal.jsx` | — |
| 298 | 🟢 Óptimo | `resources/js/Pages/Gestor/Assets/Types/Partials/MetadataFieldEditor.jsx` | — |
| 297 | 🟢 Óptimo | `resources/js/Pages/Gestor/Contracts/Partials/AnnualPlanMatrix.jsx` | — |
| 289 | 🟢 Óptimo | `resources/js/Pages/Tecnico/MyAssignments.jsx` | — |
| 274 | 🟢 Óptimo | `resources/js/Pages/Tecnico/Inspections/Show.jsx` | — |

## Controladores Backend (Laravel) (umbral 200 lín · crítico 300 lín)

Archivos: 32 · sobre umbral: **0** · críticos: **0** · con acceso directo a datos: **11**

| Líneas | Estado | Archivo | Acceso a datos |
|---:|---|---|---|
| 198 | 🔵 Moderado | `app/Http/Controllers/AssetTypeController.php` | `::query\(` (L25), `::where\(` (L41), `::create\(` (L106), `::findOrFail\(` (L102) |
| 197 | 🔵 Moderado | `app/Http/Controllers/Admin/ConsultantController.php` | — |
| 191 | 🔵 Moderado | `app/Http/Controllers/AdministrationController.php` | — |
| 190 | 🔵 Moderado | `app/Http/Controllers/TrainingController.php` | — |
| 186 | 🔵 Moderado | `app/Http/Controllers/AssetController.php` | — |
| 185 | 🔵 Moderado | `app/Http/Controllers/HelpCenterController.php` | — |
| 185 | 🔵 Moderado | `app/Http/Controllers/PasswordResetRequestController.php` | `::where\(` (L28), `::with\(` (L85), `::create\(` (L68) |
| 184 | 🔵 Moderado | `app/Http/Controllers/IncidenteController.php` | `DB::` (L132), `::query\(` (L31), `::with\(` (L92), `::create\(` (L134), `::find\(` (L99) |
| 165 | 🔵 Moderado | `app/Http/Controllers/CompanyController.php` | — |
| 164 | 🔵 Moderado | `app/Http/Controllers/OperationController.php` | — |
| 155 | 🔵 Moderado | `app/Http/Controllers/CategoryController.php` | `::query\(` (L18), `::create\(` (L81), `::findOrFail\(` (L77) |
| 149 | 🔵 Moderado | `app/Http/Controllers/Contracts/AnnualContractReportController.php` | `::where\(` (L29), `::with\(` (L24), `::create\(` (L26) |
| 121 | 🔵 Moderado | `app/Http/Controllers/SignatureApprovalController.php` | `::where\(` (L22) |
| 118 | 🟢 Óptimo | `app/Http/Controllers/VisitCertificatePDFController.php` | `::with\(` (L14) |
| 110 | 🟢 Óptimo | `app/Http/Controllers/FindingController.php` | — |
| 108 | 🟢 Óptimo | `app/Http/Controllers/InspectionController.php` | — |
| 108 | 🟢 Óptimo | `app/Http/Controllers/TrainingPDFController.php` | `::with\(` (L18) |
| 107 | 🟢 Óptimo | `app/Http/Controllers/MasterReportController.php` | — |
| 105 | 🟢 Óptimo | `app/Http/Controllers/ProfileController.php` | — |
| 82 | 🟢 Óptimo | `app/Http/Controllers/ContractController.php` | — |
| 74 | 🟢 Óptimo | `app/Http/Controllers/ChecklistController.php` | — |
| 66 | 🟢 Óptimo | `app/Http/Controllers/InspectionPDFController.php` | `::with\(` (L17) |
| 65 | 🟢 Óptimo | `app/Http/Controllers/Admin/GlobalStaffController.php` | — |
| 56 | 🟢 Óptimo | `app/Http/Controllers/Auth/LoginController.php` | — |
| 56 | 🟢 Óptimo | `app/Http/Controllers/Hallazgos/HallazgoPDFController.php` | `::with\(` (L17) |
| 54 | 🟢 Óptimo | `app/Http/Controllers/Gestor/AssignmentManagerController.php` | — |
| 52 | 🟢 Óptimo | `app/Http/Controllers/Auth/ForcePasswordChangeController.php` | — |
| 52 | 🟢 Óptimo | `app/Http/Controllers/Incidentes/IncidentePDFController.php` | `::with\(` (L17) |
| 48 | 🟢 Óptimo | `app/Http/Controllers/PublicSignatureController.php` | — |
| 46 | 🟢 Óptimo | `app/Http/Controllers/SwitchRoleController.php` | — |

## Servicios, Jobs y Listeners (Dominio) (umbral 250 lín · crítico 400 lín)

Archivos: 21 · sobre umbral: **10** · críticos: **5**

| Líneas | Estado | Archivo | Acceso a datos |
|---:|---|---|---|
| 567 | 🔴 Crítico | `app/Services/InspectionService.php` | — |
| 512 | 🔴 Crítico | `app/Services/AnnualContractService.php` | — |
| 471 | 🔴 Crítico | `app/Services/OperationService.php` | — |
| 448 | 🔴 Crítico | `app/Services/MasterReportService.php` | — |
| 401 | 🔴 Crítico | `app/Services/TechnicianAssignmentService.php` | — |
| 318 | 🟡 Sobre umbral | `app/Services/ConsultantService.php` | — |
| 310 | 🟡 Sobre umbral | `app/Services/DashboardMetricsService.php` | — |
| 284 | 🟡 Sobre umbral | `app/Services/AssetService.php` | — |
| 265 | 🟡 Sobre umbral | `app/Services/UserManagementService.php` | — |
| 252 | 🟡 Sobre umbral | `app/Services/TrainingService.php` | — |
| 197 | 🔵 Moderado | `app/Services/ChecklistBuilderService.php` | — |
| 195 | 🔵 Moderado | `app/Services/CompanyService.php` | — |
| 182 | 🔵 Moderado | `app/Services/FindingService.php` | — |
| 177 | 🔵 Moderado | `app/Services/TimeTrackingService.php` | — |
| 151 | 🔵 Moderado | `app/Services/VisitService.php` | — |
| 144 | 🟢 Óptimo | `app/Services/GestorToolsService.php` | — |
| 135 | 🟢 Óptimo | `app/Jobs/ImportEmployeesJob.php` | — |
| 124 | 🟢 Óptimo | `app/Services/ProfileService.php` | — |
| 124 | 🟢 Óptimo | `app/Listeners/SyncAssetMetadata.php` | — |
| 68 | 🟢 Óptimo | `app/Services/BrandingService.php` | — |
| 21 | 🟢 Óptimo | `app/Listeners/LogClientAccess.php` | — |

**Acción sugerida:** Dividir servicios grandes por caso de uso o responsabilidad única

