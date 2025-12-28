# MasterDemoSeeder - Documentación Técnica

> **Versión:** 1.0.0  
> **Fecha:** 28 de Diciembre de 2024  
> **Autor:** Sistema EP-Back

---

## 📋 Índice

1. [Descripción General](#descripción-general)
2. [Arquitectura](#arquitectura)
3. [Estructura de Archivos](#estructura-de-archivos)
4. [Casos de Uso](#casos-de-uso)
5. [Datos Generados](#datos-generados)
6. [Uso](#uso)
7. [Mantenimiento](#mantenimiento)
8. [Troubleshooting](#troubleshooting)

---

## 📖 Descripción General

El **MasterDemoSeeder** es un sistema modular de seeders diseñado para poblar la base de datos con datos de demostración realistas. Genera información completa para cuatro casos de uso diferentes (Construcción, Bodega, Minería, Transporte), cubriendo todos los módulos del sistema.

### Objetivos

- ✅ Generar datos realistas para demostración MVP
- ✅ Poblar todos los módulos del sistema
- ✅ Facilitar testing de dashboards y funcionalidades
- ✅ Mantener código modular y reutilizable
- ✅ Permitir ejecución individual por caso de uso

---

## 🏗️ Arquitectura

### Patrón de Diseño

El seeder utiliza un patrón **Orquestador + Módulos** con un **Trait de Helpers**:

```
MasterDemoSeeder (Orquestador)
    ├── Crea usuarios globales (Admin + Técnicos)
    └── Ejecuta seeders de casos de uso
        ├── ConstructionSeeder
        ├── WinerySeeder
        ├── MiningSeeder
        └── TransportSeeder
            └── Usan SeederHelpers (Trait)
```

### Ventajas

- **Modularidad:** Cada caso de uso es independiente
- **Reutilización:** Métodos compartidos en trait
- **Mantenibilidad:** Fácil de actualizar y extender
- **Flexibilidad:** Ejecutar todo o casos individuales

---

## 📁 Estructura de Archivos

```
database/seeders/
├── MasterDemoSeeder.php              # Orquestador principal
├── CategorySeeder.php                # Categorías (prerequisito)
├── Traits/
│   └── SeederHelpers.php            # Métodos compartidos
└── UseCases/
    ├── ConstructionSeeder.php       # Caso: Construcción
    ├── WinerySeeder.php             # Caso: Bodega
    ├── MiningSeeder.php             # Caso: Minería
    └── TransportSeeder.php          # Caso: Transporte
```

### MasterDemoSeeder.php

**Responsabilidades:**
- Crear administrador global
- Crear 7 técnicos (4 casos de uso)
- Instanciar y ejecutar seeders de casos de uso
- Mostrar resumen de datos creados

**Métodos principales:**
- `run()`: Método principal de ejecución
- `createAdmin()`: Crea usuario administrador
- `createTechnicians()`: Crea 7 técnicos
- `showSummary()`: Muestra resumen de datos

### SeederHelpers.php (Trait)

**Métodos disponibles:**

| Método | Descripción | Parámetros |
|--------|-------------|------------|
| `createChecklistWithSections()` | Crea checklist con secciones | title, description, category, sectionsData |
| `createChecklistWithoutSections()` | Crea checklist sin secciones | title, description, category, questionsData |
| `createInspections()` | Genera inspecciones con respuestas | checklists, tecnicos, sede, count |
| `createTrainings()` | Crea capacitaciones y asistencias | sede, tecnicos, trainingsData, employees |
| `createEmployees()` | Crea empleados | sede, employeesData |
| `createEppItems()` | Crea items de EPP | itemsData |
| `createEppDeliveries()` | Registra entregas de EPP | sede, employees, tecnicos, eppItems, count |
| `createRiskMatrix()` | Genera matriz de riesgos | sede, tecnico, title, description, area, process, risksData |
| `createAnnualContract()` | Crea contrato anual | sede, checklists, industry |
| `createDemoAssignments()` | Genera tareas pendientes/vencidas | checklists, tecnico, sede |
| `generateAnswerValue()` | Genera respuestas contextuales | question |
| `getDefaultSignature()` | Retorna firma base64 | - |

### Seeders de Casos de Uso

Cada seeder de caso de uso sigue la misma estructura:

**Métodos:**
1. `run()`: Ejecuta el flujo completo
2. `createCompanyAndSede()`: Crea empresa y sede
3. `createChecklists()`: Define checklists específicos
4. `seedEmployees()`: Crea empleados del sector
5. `seedTrainings()`: Define capacitaciones
6. `seedEpp()`: Crea items EPP y entregas
7. `seedRiskMatrix()`: Define riesgos del sector
8. `setTechnicians()`: Recibe técnicos del orquestador

---

## 🎯 Casos de Uso

### 1. Construcción (ConstructionSeeder)

**Empresa:** Constructora del Sur SA  
**Sede:** Obra Torre Nordelta (Tigre, Buenos Aires)  
**Técnicos:** Juan Pérez, María González

**Checklists:**
- ✅ Inspección de Andamios (CON secciones)
  - Estructura Base
  - Plataformas de Trabajo
  - Protecciones Colectivas
- ✅ Control de Excavaciones (CON secciones)
  - Apuntalamiento
  - Accesos y Salidas
  - Señalización
- ✅ Orden y Limpieza en Obra (SIN secciones)

**Capacitaciones:**
- Trabajo en Altura
- Uso de Arnés de Seguridad
- Señalización de Obra
- Primeros Auxilios en Obra
- Manejo de Herramientas Eléctricas
- Prevención de Riesgos

**Empleados (10):**
- Oficial Albañil, Ayudante, Oficial Electricista, Oficial Plomero, Operario, Capataz, Oficial Carpintero

**EPP (5 items):**
- Casco MSA V-Gard Blanco
- Arnés de Seguridad Parquer
- Botas de Seguridad Pampero
- Guantes de Cuero Reforzado
- Chaleco Reflectivo Naranja

**Riesgos (5):**
- Caída de altura desde andamios
- Golpe por caída de objetos
- Electrocución por contacto eléctrico
- Atrapamiento en excavaciones
- Sobreesfuerzo por manipulación manual

---

### 2. Bodega (WinerySeeder)

**Empresa:** Bodegas del Valle SRL  
**Sede:** Bodega Mendoza (Luján de Cuyo, Mendoza)  
**Técnicos:** Carlos Rodríguez, Ana Martínez

**Checklists:**
- ✅ Control de Tanques de Fermentación (CON secciones)
  - Estado Estructural
  - Válvulas y Conexiones
  - Instrumentación
- ✅ Inspección de Barricas de Roble (CON secciones)
  - Estado de Madera
  - Cercos y Tapones
  - Identificación
- ✅ Higiene y Sanitización (SIN secciones)

**Capacitaciones:**
- Manejo de Productos Químicos
- Espacios Confinados
- Higiene en Bodega
- Cata y Calidad
- Mantenimiento de Barricas
- Prevención de Incendios

**Empleados (10):**
- Enólogo, Operario de Bodega, Técnico en Fermentación, Supervisor de Calidad, Técnico de Laboratorio, Encargado de Barricas

**EPP (5 items):**
- Guantes Nitrilo Resistente Químicos
- Máscara Respiratoria 3M 6200
- Delantal PVC Impermeable
- Botas de Goma Blancas
- Protector Facial Transparente

**Riesgos (4):**
- Intoxicación por gases (CO2, SO2)
- Quemaduras por productos químicos
- Caída en escaleras de tanques
- Incendio por alcohol

---

### 3. Minería (MiningSeeder)

**Empresa:** Minera Andina SA  
**Sede:** Mina San Juan (Calingasta, San Juan)  
**Técnicos:** Pedro López, Laura Fernández

**Checklists:**
- ✅ Inspección de Maquinaria Pesada (CON secciones)
  - Sistema Hidráulico
  - Sistema Eléctrico
  - Cabina y Controles
- ✅ Control de Voladuras (CON secciones)
  - Almacenamiento de Explosivos
  - Perforación
  - Carga y Detonación
- ✅ Ventilación de Túneles (SIN secciones)

**Capacitaciones:**
- Operación de Maquinaria Pesada
- Uso de Explosivos
- Rescate en Minas
- Primeros Auxilios Mineros
- Ventilación en Minería
- Prevención de Derrumbes

**Empleados (10):**
- Operador de Pala, Perforista, Volador, Operador de Camión, Supervisor de Turno, Mecánico

**EPP (5 items):**
- Casco Minero con Lámpara LED
- Respirador Media Cara MSA
- Botas Mineras Reforzadas
- Guantes de Cuero Manga Larga
- Protector Auditivo Tipo Copa

**Riesgos (5):**
- Derrumbe de galerías
- Explosión por gases
- Atrapamiento por maquinaria
- Exposición a polvo de sílice
- Caída de rocas

---

### 4. Transporte (TransportSeeder)

**Empresa:** Transportes del Norte SA  
**Sede:** Centro de Distribución Rosario (Rosario, Santa Fe)  
**Técnico:** Roberto Sánchez

**Checklists:**
- ✅ Inspección de Vehículos de Carga (CON secciones)
  - Sistema de Frenos
  - Neumáticos
  - Iluminación y Señalización
- ✅ Control de Depósito (CON secciones)
  - Orden y Limpieza
  - Estanterías
- ✅ Verificación de Documentación (SIN secciones)

**Capacitaciones:**
- Manejo Defensivo
- Carga y Descarga Segura
- Primeros Auxilios en Ruta
- Mantenimiento Preventivo
- Normativa de Transporte
- Gestión de Residuos

**Empleados (10):**
- Chofer Larga Distancia, Chofer Urbano, Operario de Depósito, Supervisor de Flota, Mecánico, Chofer de Reparto

**EPP (5 items):**
- Chaleco Reflectivo Alta Visibilidad
- Guantes Anticorte
- Calzado de Seguridad con Puntera
- Casco de Seguridad
- Protector Auditivo Tipo Tapón

**Asignaciones (7):**
- **2 Vencidas:** Tareas críticas con fecha expirada
- **5 Pendientes:** Programadas para los próximos 10 días

**Riesgos (5):**
- Accidente de tránsito
- Caída de carga durante manipulación
- Sobreesfuerzo en carga/descarga
- Atropello en depósito
- Fatiga del conductor

---

## 📊 Datos Generados

### Resumen por Módulo

| Módulo | Cantidad | Detalle |
|--------|----------|---------|
| **Usuarios** | 8 | 1 admin + 7 técnicos |
| **Empresas** | 4 | 1 por caso de uso |
| **Sedes** | 4 | 1 por empresa |
| **Checklists** | 12 | 8 con secciones, 4 sin |
| **Secciones** | ~28 | ~7 por checklist con secciones |
| **Preguntas** | ~80 | ~7 por checklist |
| **Inspecciones** | 95 | ~30 por caso (Transp: 5) |
| **Respuestas** | ~600 | ~6 por inspección |
| **Capacitaciones** | 24 | 6 por caso de uso |
| **Asistencias** | ~150 | 5-10 por capacitación pasada |
| **Empleados** | 40 | 10 por caso de uso |
| **Items EPP** | 20 | 5 por caso de uso |
| **Entregas EPP** | 40 | 10 por caso de uso |
| **Matrices Riesgo** | 4 | 1 por caso de uso |
| **Riesgos** | 19 | 4-5 por matriz |
| **Controles** | ~57 | 2-3 por riesgo |
| **Evaluaciones** | 19 | 1 por riesgo |
| **Contratos Anuales** | 4 | 1 por sede |
| **Actividades** | 48 | 12 por contrato |
| **Asignaciones** | 7 | Específicas para Transporte |
| **TOTAL** | **~1200** | registros |

### Distribución Temporal

- **Inspecciones:** Últimos 90 días
- **Capacitaciones Pasadas:** -15 a -75 días
- **Capacitaciones Futuras:** +5 a +45 días
- **Entregas EPP:** Últimos 90 días
- **Evaluaciones de Riesgo:** Últimos 30 días

---

## 🚀 Uso

### Ejecución Completa

```bash
# 1. Limpiar base de datos
php artisan migrate:fresh --force

# 2. Cargar categorías (REQUERIDO)
php artisan db:seed --class=CategorySeeder

# 3. Ejecutar seeder completo
php artisan db:seed --class=MasterDemoSeeder
```

### Ejecución Individual

```bash
# Solo Construcción
php artisan db:seed --class=UseCases\\ConstructionSeeder

# Solo Bodega
php artisan db:seed --class=UseCases\\WinerySeeder

# Solo Minería
php artisan db:seed --class=UseCases\\MiningSeeder

# Solo Transporte
php artisan db:seed --class=UseCases\\TransportSeeder
```

### Credenciales Generadas

**Administrador:**
```
Email: admin@ep.com
Password: password
```

**Técnicos:**
```
Construcción:
  - jperez@ep.com / password
  - mgonzalez@ep.com / password

Bodega:
  - crodriguez@ep.com / password
  - amartinez@ep.com / password

Minería:
  - plopez@ep.com / password
  - lfernandez@ep.com / password

Transporte:
  - rsanchez@ep.com / password
```

---

## 🔧 Mantenimiento

### Agregar Nuevo Caso de Uso

1. **Crear nuevo seeder:**
```bash
php artisan make:seeder UseCases/HospitalSeeder
```

2. **Implementar estructura:**
```php
<?php

namespace Database\Seeders\UseCases;

use Illuminate\Database\Seeder;
use Database\Seeders\Traits\SeederHelpers;
use App\Models\{User, Empresa, Sede, Category};

class HospitalSeeder extends Seeder
{
    use SeederHelpers;

    private $tecnicos = [];
    private $sede;

    public function run(): void
    {
        echo "🏥 Creando caso de uso: Hospital...\n";

        $this->createCompanyAndSede();
        $checklists = $this->createChecklists();
        $this->createInspections($checklists, $this->tecnicos, $this->sede, 10);
        $employees = $this->seedEmployees();
        $this->seedTrainings($employees);
        $this->seedEpp($employees);
        $this->seedRiskMatrix();
        $this->createAnnualContract($this->sede, $checklists, 'Hospital');

        echo "✅ Hospital completado\n";
    }

    // Implementar métodos privados...

    public function setTechnicians(array $tecnicos): void
    {
        $this->tecnicos = $tecnicos;
    }
}
```

3. **Agregar a MasterDemoSeeder:**
```php
// En run()
$hospitalSeeder = new HospitalSeeder();
$hospitalSeeder->setTechnicians([$tecnicos[6], $tecnicos[7]]);
$hospitalSeeder->run();
```

### Modificar Datos Existentes

Para cambiar datos de un caso de uso, editar el seeder correspondiente:

```php
// database/seeders/UseCases/ConstructionSeeder.php

private function seedEmployees(): array
{
    return $this->createEmployees($this->sede, [
        // Agregar o modificar empleados aquí
        ['Nuevo', 'Empleado', '55123456', '20-55123456-0', 'Nuevo Puesto'],
    ]);
}
```

### Agregar Nuevo Método Helper

En `SeederHelpers.php`:

```php
protected function createNewEntity($param1, $param2): void
{
    // Implementación
}
```

---

## 🐛 Troubleshooting

### Error: "UNIQUE constraint failed: users.email"

**Causa:** Intentar ejecutar el seeder sin limpiar la base de datos primero.

**Solución:**
```bash
php artisan migrate:fresh --force
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=MasterDemoSeeder
```

### Error: "table X has no column named Y"

**Causa:** Desincronización entre seeder y migración.

**Solución:**
1. Verificar campos en migración: `database/migrations/*_create_X_table.php`
2. Actualizar seeder para usar nombres correctos
3. Ejecutar `migrate:fresh` de nuevo

### Error: "Call to undefined method"

**Causa:** Método no existe en el trait o modelo.

**Solución:**
1. Verificar que el trait `SeederHelpers` esté importado: `use SeederHelpers;`
2. Verificar nombre del método
3. Verificar que el modelo tenga la relación definida

### Seeder muy lento

**Causa:** Muchas queries individuales.

**Optimización:**
```php
// Usar transacciones
DB::transaction(function () {
    // Código del seeder
});

// Deshabilitar eventos temporalmente
Model::unguard();
// ... crear registros
Model::reguard();
```

### Datos no aparecen en dashboard

**Causa:** Relaciones no cargadas o filtros incorrectos.

**Verificación:**
```bash
# Verificar en tinker
php artisan tinker
>>> User::count()
>>> DynamicChecklist::count()
>>> DynamicInspection::count()
```

---

## 📝 Notas Importantes

### Dependencias

- **CategorySeeder DEBE ejecutarse ANTES** de MasterDemoSeeder
- Las categorías (hys, calidad, ambiente) son requeridas para checklists

### Orden de Ejecución Interno

Cada caso de uso sigue este orden:
1. Empresa y Sede
2. Checklists
3. Inspecciones
4. Empleados
5. Capacitaciones (usan empleados)
6. EPP (usan empleados)
7. Matriz de Riesgos
8. Contrato Anual

**No alterar este orden** ya que hay dependencias entre entidades.

### Campos Críticos

Algunos campos tienen validaciones estrictas:

- **DNI:** 7-8 dígitos
- **CUIL:** Formato XX-XXXXXXXX-X con dígito verificador
- **Email:** Único en la tabla users
- **Firma:** Base64 válido
- **Dates:** Formato Y-m-d H:i:s

---

## 🔄 Changelog

### v1.0.0 (2024-12-28)
- ✅ Implementación inicial
- ✅ 3 casos de uso (Construcción, Bodega, Minería)
- ✅ Trait SeederHelpers con 11 métodos
- ✅ ~1000 registros generados
- ✅ Todos los módulos cubiertos

### v1.1.0 (2024-12-28)
- ✅ Agregado caso de uso: **Transporte** (Roberto Sánchez)
- ✅ Implementación de **Asignaciones de Tareas** (vencidas y pendientes)
- ✅ Actualización de contadores globales y resumen final
- ✅ Mejoras en filtros de categorías para dashboard admin
- ✅ Corrección de rutas en dashboard técnico

---

## 📚 Referencias

- [Laravel Seeding Documentation](https://laravel.com/docs/seeding)
- [Database Factories](https://laravel.com/docs/database-testing#defining-model-factories)
- [Eloquent Relationships](https://laravel.com/docs/eloquent-relationships)

---

**Mantenido por:** Equipo de Desarrollo EP-Back  
**Última actualización:** 28 de Diciembre de 2024
