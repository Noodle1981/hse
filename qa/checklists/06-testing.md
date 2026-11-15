# 06 — Testing y Verificación Automatizada (PHPUnit 11 + Laravel 12)

- [ ] **Suite de pruebas unitarias de dominio (`tests/Unit`)**: Pruebas automatizadas para `TimeTrackingService` (cálculo de distancia GPS Haversine, tolerancia en metros, horarios operativos y Roster 24/7), `VisitService` y `ValidCuil`.
- [ ] **Pruebas de integración e Inertia (`tests/Feature`)**: Pruebas HTTP verificando renderizado de componentes Inertia (`assertInertia`), máquina de estados de `MasterReport` (`solicitado` → `en_redaccion` → `finalizado` → `aprobado`) y sincronización `SyncAssetMetadata`.
- [ ] **Pruebas de aislamiento Multi-Tenant y Roles (RBAC)**: Casos de prueba que confirman denegación `403` cuando un `vendedor` intenta eliminar una consultora o gestionar staff global, o cuando un `Gestor` intenta acceder a empresas/sedes de otra consultora.
- [ ] **Purga de tests Legacy rotos (`tests/Legacy`)**: Actualizar o remover pruebas antiguas de componentes Livewire eliminados para que `php artisan test` pase al 100% en verde.
- [ ] **Uso de base de datos efímera en memoria**: `phpunit.xml` configurado con `DB_CONNECTION=sqlite` y `DB_DATABASE=:memory:` usando el trait `RefreshDatabase` para no tocar `database/database.sqlite` local durante los tests.
- [ ] **Faking de servicios y almacenamiento**: Uso de `Storage::fake('public')`, `Mail::fake()` y `Event::fake()` en pruebas de carga de fotos, firmas y envío de constancias por correo.
