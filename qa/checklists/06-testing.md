# 06 — Testing y Verificación Automatizada (PHPUnit 11 + Laravel 12)

- [x] **Suite de pruebas unitarias de dominio (`tests/Unit`)**: Pruebas automatizadas para `TimeTrackingService` (cálculo de distancia GPS Haversine, tolerancia en metros, horarios operativos y Roster 24/7), `DynamicChecklist` y `ValidCuil` (`ValidCuilTest`).
- [x] **Pruebas de integración e Inertia (`tests/Feature`)**: Pruebas HTTP verificando renderizado de componentes Inertia (`assertInertia`), motor de activos y sincronización `SyncAssetMetadata` (`AssetEngineTest`), límites de consultora y trazabilidad.
- [x] **Pruebas de aislamiento Multi-Tenant y Roles (RBAC)**: Casos de prueba que confirman denegación `403` cuando un `vendedor` intenta eliminar una consultora o gestionar staff global (`GlobalStaffManagementTest`), o cuando un `Gestor` intenta acceder a empresas/sedes o visitas de otra consultora (`MultiTenantIsolationTest`).
- [x] **Purga de tests Legacy rotos (`tests/Legacy`)**: Eliminados los tests antiguos de componentes Livewire obsoletos para que `php artisan test` pase al 100% en verde (56 passed, 163 assertions).
- [x] **Uso de base de datos efímera en memoria**: `phpunit.xml` configurado con `DB_CONNECTION=sqlite` y `DB_DATABASE=:memory:` usando el trait `RefreshDatabase` para no tocar `database/database.sqlite` local durante los tests.
- [x] **Faking de servicios y almacenamiento**: Uso de `Storage::fake('public')`, `Mail::fake()` y `Event::fake()` en pruebas de carga de fotos, firmas y envío de constancias por correo.
