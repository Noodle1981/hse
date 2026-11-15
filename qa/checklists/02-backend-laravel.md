# 02 — Backend y Arquitectura (PHP 8.5 / Laravel 12)

- [x] **Capas y separación de responsabilidades (obligatorio)**: Arquitectura en capas claramente delimitada (`routes/web.php` → `Controllers` → `FormRequests` / `Policies` → `Services` / `Events` → `Models` Eloquent).
- [x] **Controladores delgados — Thin Controllers < 200 lín (obligatorio)**: Los controladores actúan como adaptadores HTTP/Inertia finos sin acumular lógica de negocio pesada ni cálculos complejos. (29/29 controladores < 200 lín).
- [x] **Cero acceso directo a datos en controladores (obligatorio)**: Delegar consultas complejas, transacciones `DB::transaction` y mutaciones multi-modelo desde los controladores hacia clases en `app/Services/` (`VisitService`, `TimeTrackingService`, `BrandingService`, `ProfileService`, `FindingService`, `ChecklistBuilderService`, `CompanyService`, `TrainingService`, `AssetService`, `MasterReportService`, `AnnualContractService`, `InspectionService`, `OperationService`, `TechnicianAssignmentService`, `DashboardMetricsService`, `ConsultantService`, `UserManagementService`).
- [x] **Compatibilidad total con PHP 8.5**: Uso de constantes y tipado compatibles con PHP 8.5 (como `\Pdo\Mysql::ATTR_SSL_CA` en `config/database.php`) sin avisos `Deprecated` en tiempo de ejecución.
- [x] **Route Model Binding consistente**: Los nombres de parámetros en `routes/web.php` coinciden con las variables tipadas en los métodos de los controladores (ej. `{masterReport}`, `{inspection}`, `{visit}`, `{asset}`).
- [x] **Uso de Form Requests dedicados**: Extraer validaciones inline (`$request->validate([...])`) de los controladores hacia clases dedicadas en `app/Http/Requests/`.
- [x] **Eventos, Listeners y Jobs desacoplados**: Procesamiento pesado (importación de empleados en `ImportEmployeesJob`) y efectos secundarios (sincronización de metadata en `SyncAssetMetadata` tras `InspectionCompleted`) gestionados mediante eventos y colas.
- [x] **Unificación de motor PDF y Excel**: Consolidado el uso de `barryvdh/laravel-dompdf` y `shuchkin/simplexlsx`, eliminando paquetes redundantes (`spatie/laravel-pdf`, `maatwebsite/excel`, `phpoffice/phpspreadsheet`, `shuchkin/simplexlsxgen`) en `AUD-005`.

## Hallazgos de la medición (Archivos sobre umbral > 200 lín y acceso a datos)

- [x] **Dividir `app/Http/Controllers/AdministrationController.php` (1332 lín · 🔴 Crítico → 191 lín)**: Extraída la gestión de Consultoras a `Admin/ConsultantController.php` + `ConsultantService.php`, Staff Global a `Admin/GlobalStaffController.php`, usuarios/técnicos a `UserManagementService.php` y métricas de dashboards a `DashboardMetricsService.php`.
- [x] **Dividir `app/Http/Controllers/OperationController.php` (1097 lín · 🔴 Crítico → 164 lín)**: Extraído el tablero de asignaciones (`myAssignments`) a `TechnicianAssignmentService.php` y operaciones de visitas a `OperationService.php` apoyado en `VisitService`.
- [x] **Dividir `app/Http/Controllers/InspectionController.php` (706 lín · 🔴 Crítico → 102 lín)**: Movida la lógica del Wizard, validación de Interlock SRE, cálculo de fallas y `relinkAsset` a `InspectionService.php`, eliminando `DB::` y `Artisan::call` del controlador.
- [x] **Dividir `app/Http/Controllers/ContractController.php` (551 lín · 🔴 Crítico → 74 lín)**: Extraída la matriz y sincronización del Plan Anual (PPD) y sus transacciones hacia `AnnualContractService.php`.
- [x] **Dividir `app/Http/Controllers/MasterReportController.php` (462 lín · 🔴 Crítico → 96 lín)**: Movida la consolidación de datos del período y la máquina de estados a `MasterReportService.php`.
- [x] **Dividir `app/Http/Controllers/AssetController.php` (420 lín · 🔴 Crítico → 149 lín)**: Extraídos filtros, KPIs, batch store, timeline de evidencias y fotos a `AssetService.php`.
- [x] **Dividir `app/Http/Controllers/TrainingController.php` (320 lín · 🔴 Crítico → 190 lín)**: Delegado el guardado de firmas PNG en disco, sesiones in-situ y asistencia nominal a `TrainingService.php`.
- [x] **Dividir `app/Http/Controllers/CompanyController.php` (305 lín · 🔴 Crítico → 165 lín)**: Separadas consultas y mutaciones de empresas y sedes en `CompanyService.php`.
- [x] **Refactorizar `app/Http/Controllers/ChecklistController.php` (296 lín · 🟡 Sobre umbral → < 200 lín)**: Extraída la creación/edición profunda de secciones y preguntas y duplicación (`duplicate`) a `ChecklistBuilderService.php`.
- [x] **Refactorizar `app/Http/Controllers/FindingController.php` (265 lín · 🟡 Sobre umbral → < 200 lín)**: Movida la creación de hallazgos con fotos y seguimientos hacia `FindingService.php`.
- [x] **Refactorizar `app/Http/Controllers/ProfileController.php` (250 lín · 🟡 Sobre umbral → < 200 lín)**: Extraído el procesamiento de fotos de perfil y firmas en estado `pending` (`firma_temp`) a `ProfileService.php`.
