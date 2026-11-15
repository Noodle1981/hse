# Ranking de complejidad — HSE SaaS (XamaSafe / EP-Back)

> Generado: 2026-11-15 · commit `4e199c8` · rama `pre-produccion`

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

Archivos: 32 · sobre umbral: **0** · críticos: **0**

| Líneas | Estado | Archivo | Acceso a datos |
|---:|---|---|---|
| 197 | 🔵 Moderado | `app/Http/Controllers/Admin/ConsultantController.php` | — |
| 191 | 🔵 Moderado | `app/Http/Controllers/AdministrationController.php` | — |
| 190 | 🔵 Moderado | `app/Http/Controllers/TrainingController.php` | — |
| 186 | 🔵 Moderado | `app/Http/Controllers/AssetController.php` | — |
| 185 | 🔵 Moderado | `app/Http/Controllers/HelpCenterController.php` | — |
| 165 | 🔵 Moderado | `app/Http/Controllers/CompanyController.php` | — |
| 164 | 🔵 Moderado | `app/Http/Controllers/OperationController.php` | — |
| 124 | 🔵 Moderado | `app/Http/Controllers/CategoryController.php` | — |
| 116 | 🟢 Óptimo | `app/Http/Controllers/SignatureApprovalController.php` | — |
| 110 | 🟢 Óptimo | `app/Http/Controllers/FindingController.php` | — |
| 108 | 🟢 Óptimo | `app/Http/Controllers/InspectionController.php` | — |
| 108 | 🟢 Óptimo | `app/Http/Controllers/PasswordResetRequestController.php` | — |
| 107 | 🟢 Óptimo | `app/Http/Controllers/MasterReportController.php` | — |
| 105 | 🟢 Óptimo | `app/Http/Controllers/ProfileController.php` | — |
| 95 | 🟢 Óptimo | `app/Http/Controllers/AssetTypeController.php` | — |
| 95 | 🟢 Óptimo | `app/Http/Controllers/VisitCertificatePDFController.php` | — |
| 92 | 🟢 Óptimo | `app/Http/Controllers/IncidenteController.php` | — |
| 82 | 🟢 Óptimo | `app/Http/Controllers/ContractController.php` | — |
| 74 | 🟢 Óptimo | `app/Http/Controllers/ChecklistController.php` | — |
| 65 | 🟢 Óptimo | `app/Http/Controllers/Admin/GlobalStaffController.php` | — |
| 56 | 🟢 Óptimo | `app/Http/Controllers/Auth/LoginController.php` | — |
| 54 | 🟢 Óptimo | `app/Http/Controllers/Gestor/AssignmentManagerController.php` | — |
| 54 | 🟢 Óptimo | `app/Http/Controllers/InspectionPDFController.php` | — |
| 52 | 🟢 Óptimo | `app/Http/Controllers/Auth/ForcePasswordChangeController.php` | — |
| 52 | 🟢 Óptimo | `app/Http/Controllers/Hallazgos/HallazgoPDFController.php` | — |
| 48 | 🟢 Óptimo | `app/Http/Controllers/Incidentes/IncidentePDFController.php` | — |
| 48 | 🟢 Óptimo | `app/Http/Controllers/PublicSignatureController.php` | — |
| 46 | 🟢 Óptimo | `app/Http/Controllers/SwitchRoleController.php` | — |
| 44 | 🟢 Óptimo | `app/Http/Controllers/Gestor/BrandingController.php` | — |
| 40 | 🟢 Óptimo | `app/Http/Controllers/TrainingPDFController.php` | — |

## Servicios, Jobs y Listeners (Dominio) (umbral 250 lín · crítico 400 lín)

Archivos: 33 · sobre umbral: **0** · críticos: **0**

| Líneas | Estado | Archivo | Acceso a datos |
|---:|---|---|---|
| 250 | 🔵 Moderado | `app/Services/DashboardMetricsService.php` | — |
| 249 | 🔵 Moderado | `app/Services/ConsultantService.php` | — |
| 249 | 🔵 Moderado | `app/Services/TrainingService.php` | — |
| 244 | 🔵 Moderado | `app/Services/AssetService.php` | — |
| 239 | 🔵 Moderado | `app/Services/MasterReportComplianceService.php` | — |
| 236 | 🔵 Moderado | `app/Services/AnnualComplianceCalculatorService.php` | — |
| 236 | 🔵 Moderado | `app/Services/UserManagementService.php` | — |
| 226 | 🔵 Moderado | `app/Services/QuickTaskExecutionService.php` | — |
| 221 | 🔵 Moderado | `app/Services/TechnicianTaskUnifierService.php` | — |
| 219 | 🔵 Moderado | `app/Services/AnnualContractService.php` | — |
| 219 | 🔵 Moderado | `app/Services/InspectionService.php` | — |
| 214 | 🔵 Moderado | `app/Services/OperationService.php` | — |
| 197 | 🔵 Moderado | `app/Services/ChecklistBuilderService.php` | — |
| 195 | 🔵 Moderado | `app/Services/CompanyService.php` | — |
| 195 | 🔵 Moderado | `app/Services/InspectionHistoryService.php` | — |
| 182 | 🔵 Moderado | `app/Services/FindingService.php` | — |
| 177 | 🔵 Moderado | `app/Services/TimeTrackingService.php` | — |
| 173 | 🔵 Moderado | `app/Services/MasterReportService.php` | — |
| 159 | 🔵 Moderado | `app/Services/TechnicianAssignmentService.php` | — |
| 151 | 🔵 Moderado | `app/Services/VisitService.php` | — |
| 144 | 🟢 Óptimo | `app/Services/GestorToolsService.php` | — |
| 135 | 🟢 Óptimo | `app/Jobs/ImportEmployeesJob.php` | — |
| 124 | 🟢 Óptimo | `app/Services/AnnualContractReportService.php` | — |
| 124 | 🟢 Óptimo | `app/Services/AssetTypeService.php` | — |
| 124 | 🟢 Óptimo | `app/Services/ProfileService.php` | — |
| 124 | 🟢 Óptimo | `app/Listeners/SyncAssetMetadata.php` | — |
| 120 | 🟢 Óptimo | `app/Services/PdfReportService.php` | — |
| 119 | 🟢 Óptimo | `app/Services/IncidenteService.php` | — |
| 114 | 🟢 Óptimo | `app/Services/InspectionAssetSyncService.php` | — |
| 102 | 🟢 Óptimo | `app/Services/PasswordResetRequestService.php` | — |

## Control de versiones

- Commits totales: 269
- Convención de commits (últimos 50): **96%**
- Tags: 1 (último: `v2.3.0`)
- Ejemplos sin convención:
  - "mejoras varias"
  - "design: update landing page copies and align contact button styling"

## Documentación

- Última actualización de docs (README.md, doc, .agent/workflows/notebooklm): 2026-11-15
- Último cambio de código: 2026-11-15
- Commits de código desde la última actualización de docs: **0**
