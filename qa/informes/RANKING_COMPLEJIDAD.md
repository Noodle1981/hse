# Ranking de complejidad — HSE SaaS (XamaSafe / EP-Back)

> Generado: 2026-11-15

## Vistas y Layouts Frontend (React + Inertia) (umbral 500 lín · crítico 800 lín)

Archivos: 56 · sobre umbral: **14** · críticos: **5**

| Líneas | Estado | Archivo | Acceso a datos |
|---:|---|---|---|
| 1317 | 🔴 Crítico | `resources/js/Pages/Tecnico/MyAssignments.jsx` | — |
| 1048 | 🔴 Crítico | `resources/js/Pages/Gestor/Assets/Index.jsx` | — |
| 1014 | 🔴 Crítico | `resources/js/Pages/Tecnico/Inspections/Wizard.jsx` | — |
| 1001 | 🔴 Crítico | `resources/js/Pages/Gestor/Assets/Types/Index.jsx` | — |
| 837 | 🔴 Crítico | `resources/js/Pages/Gestor/Assets/Create.jsx` | — |
| 793 | 🟡 Sobre umbral | `resources/js/Pages/Admin/Consultants/Edit.jsx` | — |
| 675 | 🟡 Sobre umbral | `resources/js/Pages/Gestor/MasterReports/Wizard.jsx` | — |
| 667 | 🟡 Sobre umbral | `resources/js/Pages/Gestor/Companies/Locations.jsx` | — |
| 652 | 🟡 Sobre umbral | `resources/js/Pages/Gestor/Contracts/Partials/AnnualPlanMatrix.jsx` | — |
| 645 | 🟡 Sobre umbral | `resources/js/Pages/Tecnico/Visits/InProgress.jsx` | — |
| 625 | 🟡 Sobre umbral | `resources/js/Pages/Gestor/Assets/Show.jsx` | — |
| 596 | 🟡 Sobre umbral | `resources/js/Pages/Gestor/Users.jsx` | — |
| 574 | 🟡 Sobre umbral | `resources/js/Pages/Tecnico/Trainings/Session.jsx` | — |
| 569 | 🟡 Sobre umbral | `resources/js/Pages/Gestor/Checklists/Builder.jsx` | — |
| 488 | 🔵 Moderado | `resources/js/Pages/Gestor/Findings/Index.jsx` | — |
| 477 | 🔵 Moderado | `resources/js/Pages/Gestor/Dashboard.jsx` | — |
| 477 | 🔵 Moderado | `resources/js/Pages/HelpCenter/Index.jsx` | — |
| 474 | 🔵 Moderado | `resources/js/Pages/Profile/Show.jsx` | — |
| 446 | 🔵 Moderado | `resources/js/Pages/Admin/Consultants/Users.jsx` | — |
| 434 | 🔵 Moderado | `resources/js/Pages/Gestor/Incidents/Index.jsx` | — |
| 430 | 🔵 Moderado | `resources/js/Pages/Admin/Staff/Index.jsx` | — |
| 401 | 🔵 Moderado | `resources/js/Pages/Gestor/Inspections/Index.jsx` | — |
| 392 | 🔵 Moderado | `resources/js/Pages/Tecnico/Visits/ActionSelector.jsx` | — |
| 390 | 🔵 Moderado | `resources/js/Pages/Gestor/Trainings/Index.jsx` | — |
| 375 | 🔵 Moderado | `resources/js/Pages/Tecnico/Inspections/History.jsx` | — |
| 375 | 🔵 Moderado | `resources/js/Pages/Tecnico/Visits/History.jsx` | — |
| 359 | 🔵 Moderado | `resources/js/Pages/Gestor/Technicians.jsx` | — |
| 356 | 🔵 Moderado | `resources/js/Pages/Tecnico/Findings/Create.jsx` | — |
| 355 | 🔵 Moderado | `resources/js/Layouts/AuthenticatedLayout.jsx` | — |
| 343 | 🔵 Moderado | `resources/js/Pages/Admin/Dashboard.jsx` | — |

**Acción sugerida:** Extraer subcomponentes (Partials/Modals) a Components/ o lógica de estado a custom hooks

## Controladores Backend (Laravel) (umbral 200 lín · crítico 300 lín)

Archivos: 29 · sobre umbral: **0** · críticos: **0** · con acceso directo a datos: **11**

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
| 52 | 🟢 Óptimo | `app/Http/Controllers/Auth/ForcePasswordChangeController.php` | — |
| 52 | 🟢 Óptimo | `app/Http/Controllers/Incidentes/IncidentePDFController.php` | `::with\(` (L17) |
| 46 | 🟢 Óptimo | `app/Http/Controllers/SwitchRoleController.php` | — |
| 9 | 🟢 Óptimo | `app/Http/Controllers/Controller.php` | — |

## Servicios, Jobs y Listeners (Dominio) (umbral 250 lín · crítico 400 lín)

Archivos: 20 · sobre umbral: **10** · críticos: **5**

| Líneas | Estado | Archivo | Acceso a datos |
|---:|---|---|---|
| 567 | 🔴 Crítico | `app/Services/InspectionService.php` | — |
| 512 | 🔴 Crítico | `app/Services/AnnualContractService.php` | — |
| 467 | 🔴 Crítico | `app/Services/OperationService.php` | — |
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
| 135 | 🟢 Óptimo | `app/Jobs/ImportEmployeesJob.php` | — |
| 124 | 🟢 Óptimo | `app/Services/ProfileService.php` | — |
| 124 | 🟢 Óptimo | `app/Listeners/SyncAssetMetadata.php` | — |
| 68 | 🟢 Óptimo | `app/Services/BrandingService.php` | — |
| 21 | 🟢 Óptimo | `app/Listeners/LogClientAccess.php` | — |

**Acción sugerida:** Dividir servicios grandes por caso de uso o responsabilidad única

