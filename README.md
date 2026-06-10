# 📋 EP-Back - Sistema de Checklists Dinámicos

> **Versión:** V1.2.0.0  
> **Stack:** TALL (Tailwind, Alpine, Livewire, Laravel 12)  
> **Estado:** ✅ Producción Ready

Sistema moderno de gestión de checklists dinámicos con dashboard de estadísticas, autenticación segura y API REST para integraciones externas.

---

## 🚀 Características Principales

- ✅ **Checklists 100% Dinámicos** - Crea plantillas desde la UI sin código
- ✅ **8 Tipos de Pregunta** - text, textarea, radio, checkbox, select, number, date, signature
- ✅ **Firma Digital** - Capture de firmas con signature_pad.js
- ✅ **Dashboard Analytics** - KPIs, gráficos y estadísticas en tiempo real
- ✅ **Sistema de Roles** - Administrador y Técnico con permisos granulares
- ✅ **Autenticación Dual** - Web sessions + API tokens (Sanctum)
- ✅ **PDF Automático** - Generación de reportes al completar inspecciones
- ✅ **Responsive** - Sidebar colapsable y diseño mobile-first

---

## 📊 Screenshots

### Dashboard
![Dashboard](doc/screenshots/dashboard.png)

### Login
![Login](doc/screenshots/login.png)

### Creador de Checklists
![Builder](doc/screenshots/builder.png)

---

## 🛠️ Stack Tecnológico

| Tecnología | Versión | Uso |
|------------|---------|-----|
| Laravel | 12.x | Framework backend |
| Livewire | 3.7.3 | Componentes reactivos |
| Tailwind CSS | 3.x | Estilos utility-first |
| Alpine.js | 3.x | Interactividad JS |
| SQLite | 3.x | Base de datos |
| Sanctum | 4.x | API authentication |
| signature_pad.js | latest | Firmas digitales |

---

## 📦 Instalación

### Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- SQLite

### Pasos

```bash
# 1. Clonar repositorio
git clone https://github.com/tu-usuario/ep-back.git
cd ep-back

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Copiar archivo de configuración
# Linux/Mac/Git Bash: cp .env.example .env
# Windows (cmd): copy .env.example .env
cp .env.example .env

# 5. Generar app key
php artisan key:generate

# 6. Crear base de datos SQLite
# Windows (PowerShell): New-Item database/database.sqlite -ItemType File
# Linux/Mac/Git Bash: touch database/database.sqlite
touch database/database.sqlite

# 7. Crear enlace simbólico de almacenamiento (Requerido para fotos/firmas)
php artisan storage:link

# 8. Ejecutar migraciones y seeders base
php artisan migrate:fresh --seed

# 9. Importar Checklists de ejemplo (Requerido)
php artisan db:seed --class=ChecklistsFromJsonSeeder

# 10. Compilar assets (Vite)
npm run build
# O para desarrollo continuo: npm run dev

# 11. Iniciar servidor
php artisan serve
```

Accede a: **http://localhost:8000/login**

---

## 🌐 Despliegue y Producción

Al subir el proyecto al servidor, ten en cuenta los siguientes requerimientos para que el guardado de firmas y la subida de fotos (perfil, hallazgos, incidentes) funcionen correctamente:

### 1. Enlace Simbólico de Almacenamiento
Laravel almacena los archivos subidos en una carpeta privada por seguridad. Es obligatorio crear el enlace público ejecutando en la terminal del servidor:
```bash
php artisan storage:link
```

### 2. Archivos y Carpetas a Transferir
Si necesitas migrar la base de datos local y las imágenes subidas al servidor:
- **Base de Datos:** Copia el archivo `database/database.sqlite`.
- **Archivos/Imágenes subidos:** Copia toda la carpeta `storage/app/public` (aquí se guardan las subcarpetas `hallazgos/`, `incidentes/` y `signatures/`).

### 3. Configuración del Servidor PHP (`php.ini`)
Las cámaras de los teléfonos móviles modernos toman fotografías de alta resolución que suelen pesar entre **3 MB y 10 MB**. Si la configuración de PHP en tu servidor limita las subidas (por defecto suele ser de 2 MB), el servidor rechazará las peticiones del celular y causará errores en la aplicación.

Asegúrate de configurar las siguientes directivas en el archivo **`php.ini`** de tu servidor:
```ini
upload_max_filesize = 20M
post_max_size = 20M
```

---

## 👤 Usuarios Demo

### Administrador (Acceso completo)
```
Email: admin@ep.com
Contraseña: password
```

### Técnicos (Sólo llenar checklists)
```
Email: tecnico1@ep.com
Contraseña: password
```
(Disponibles: tecnico1 a tecnico5)

---

## 📂 Estructura del Proyecto

```
ep-back/
├── app/
│   ├── Livewire/
│   │   ├── Auth/
│   │   │   └── Login.php              # Login component
│   │   └── Checklists/
│   │       ├── Dashboard.php          # Estadísticas
│   │       ├── ChecklistIndex.php     # Listado
│   │       ├── ChecklistBuilder.php   # CRUD
│   │       ├── ChecklistFiller.php    # Llenar + firma
│   │       ├── InspectionHistory.php  # Historial
│   │       └── InspectionShow.php     # Detalle
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/UserController.php    # API usuarios
│   │   │   └── InspectionPDFController.php # PDF generation
│   │   └── Middleware/
│   │       └── CheckRole.php          # Middleware roles
│   └── Models/
│       ├── DynamicChecklist.php
│       ├── DynamicQuestion.php
│       ├── DynamicInspection.php
│       └── DynamicAnswer.php
├── database/
│   ├── migrations/                    # Migraciones
│   └── seeders/
│       └── DemoChecklistsSeeder.php   # Datos demo
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php          # Layout principal
│       │   └── guest.blade.php        # Layout login
│       ├── livewire/                  # Vistas Livewire
│       └── errors/                    # 404, 403
├── routes/
│   ├── web.php                        # Rutas web
│   └── api.php                        # Rutas API
└── doc/
    ├── ARQUITECTURA.md                # Arquitectura del sistema
    ├── ROADMAP.md                     # Fases del proyecto
    └── CHECKLISTS_DINAMICOS.md        # Guía técnica
```

---

## 🔐 Seguridad

### Rutas Protegidas

Todas las rutas de checklists requieren autenticación:

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class);
    Route::prefix('checklists')->group(...);
});
```

### Permisos por Rol

| Acción | Administrador | Técnico |
|--------|---------------|---------|
| Ver dashboard | ✅ | ✅ |
| Ver checklists | ✅ | ✅ |
| **Crear** checklist | ✅ | ❌ |
| **Editar** checklist | ✅ | ❌ |
| **Llenar** checklist | ✅ | ✅ |
| Ver historial | ✅ | ✅ |

---

## 🔌 API REST

### Autenticación

```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@ep.com",
  "password": "password"
}

# Respuesta
{
  "user": {...},
  "token": "1|abc123..."
}
```

### Uso

```bash
GET /api/listar_colaboradores_admin
Authorization: Bearer {token}
```

Ver [ARQUITECTURA.md](doc/ARQUITECTURA.md) para endpoints completos.

---

## 📈 Dashboard

El dashboard provee:

- **KPIs:** Total plantillas, inspecciones, mes actual, promedio diario
- **Gráfico 1:** Inspecciones por checklist (barras horizontales)
- **Gráfico 2:** Actividad últimos 30 días (barras verticales)
- **Tabla:** 10 inspecciones más recientes

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter="DynamicChecklist"

# Resultado esperado: 26 tests, 57 assertions ✅
```

---

## 📝 Datos Demo

El seeder crea:

- **8 checklists** de diferentes categorías
- **102 inspecciones** distribuidas en 90 días
- **6 usuarios:** 1 Admin + 5 Técnicos

```bash
php artisan db:seed --class=DemoChecklistsSeeder
```

---

## 🎨 Personalización

### Colores

Edita `tailwind.config.js`:

```javascript
colors: {
  'ep-primary': '#F69239',      // Naranja EP
  'ep-dark': '#1E1E2D',          // Sidebar
}
```

### Logo

Reemplaza el logo en `resources/views/layouts/app.blade.php`:

```html
<span class="text-white font-bold text-3xl">EP</span>
```

---

## 🗺️ Roadmap

- [x] **Fase 1-7:** Sistema TALL completo ✅
- [x] **Dashboard** con estadísticas ✅
- [x] **Autenticación** y roles ✅
- [ ] **Fase 8:** Theming white-label
- [ ] **Fase 9:** Notificaciones por email
- [ ] **Fase 10:** App móvil

Ver [ROADMAP.md](doc/ROADMAP.md) para detalles.

---

## 📚 Documentación

- [ARQUITECTURA.md](doc/ARQUITECTURA.md) - Arquitectura TALL + API
- [ROADMAP.md](doc/ROADMAP.md) - Fases del proyecto
- [CHECKLISTS_DINAMICOS.md](doc/CHECKLISTS_DINAMICOS.md) - Guía técnica

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crea tu feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la branch (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

## 📄 Licencia

Proyecto privado - EP Consultora © 2024

---

## 👨‍💻 Autor

**EP Consultora**  
Sistema desarrollado con ❤️ usando Laravel 12 + Livewire 3

---

## 📞 Soporte

Para soporte técnico o consultas:
- Email: soporte@ep.com
- Web: https://equipopreventivo.com.ar

---

*Última actualización: 24 Diciembre 2024*
