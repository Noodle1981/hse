# 🛠️ Guía de Desarrollo — XamaSafe

Documento para el equipo. Explica el stack, el flujo de ramas y los comandos del día a día.

---

## 📦 Stack técnico

| Capa | Tecnología |
|---|---|
| Backend | Laravel 12 (PHP) |
| Frontend | React 19 + Inertia.js |
| Estilos | Tailwind CSS v4 |
| Build | Vite 8 |
| Base de datos | MySQL (producción) / SQLite (tests) |
| PDF | DomPDF vía Laravel |

> **Importante:** El frontend vive en `resources/js/Pages/`. No hay API separada.  
> Inertia.js conecta Laravel con React — el backend devuelve props, React las renderiza.

---

## 🌿 Ramas del repositorio

Solo hay **dos ramas permanentes**:

```
main         ← desarrollo activo, siempre debe estar funcional
produccion   ← snapshot exacto de lo que corre en el servidor
```

### Regla de oro
- `main` = código que ya fue probado localmente
- `produccion` = lo que el servidor usa (solo se actualiza cuando `main` está validado)

---

## 🔄 Flujo de trabajo diario

### 1. Antes de empezar a trabajar
```bash
git checkout main
git pull origin main
```

### 2. Desarrollar y guardar cambios
```bash
# ... hacés cambios ...
git add -A
git commit -m "tipo: descripción corta de qué hiciste"
git push origin main
```

### Tipos de commit recomendados
| Prefijo | Usar cuando... |
|---|---|
| `feat:` | nueva funcionalidad |
| `fix:` | corrección de bug |
| `chore:` | tareas de mantenimiento, limpieza |
| `docs:` | solo documentación |
| `refactor:` | reorganización de código sin cambiar comportamiento |

**Ejemplo:** `git commit -m "feat: agregar filtro por empresa en listado de visitas"`

---

## 🚀 Pasar cambios a producción

Cuando `main` está validado y querés deployar:

```bash
# 1. Mergear main en produccion
git checkout produccion
git merge main --no-ff
git push origin produccion

# 2. En el servidor (SSH)
git pull origin produccion
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## 💻 Comandos de desarrollo local

### Iniciar el entorno
```bash
# Terminal 1 — backend Laravel
php artisan serve

# Terminal 2 — frontend Vite (hot reload)
npm run dev
```

### Verificar que todo está bien antes de commitear
```bash
# Tests backend (debe dar 22/22 ✅)
php artisan test

# Linter frontend (debe dar 0 errores ✅)
npm run lint
```

### Instalar dependencias (primera vez o después de un pull)
```bash
composer install
npm install --legacy-peer-deps   # ← el flag es necesario por compatibilidad de versiones
```

> **Nota sobre `--legacy-peer-deps`:** se necesita porque `laravel-vite-plugin` tiene
> una discrepancia de peer deps con Vite v8. Es esperado, no es un error.

---

## 📁 Estructura de carpetas clave

```
ep-back/
├── app/
│   ├── Http/Controllers/     ← lógica de cada módulo
│   ├── Models/               ← modelos Eloquent
│   └── Services/             ← lógica de negocio reutilizable
├── resources/
│   ├── js/
│   │   ├── app.jsx           ← entry point de React
│   │   ├── Layouts/          ← layouts compartidos (sidebar, nav)
│   │   └── Pages/            ← una carpeta por rol
│   │       ├── Auth/         ← login
│   │       ├── Gestor/       ← vistas del administrador
│   │       ├── Tecnico/      ← vistas del técnico
│   │       └── Admin/        ← super admin
│   └── css/
│       └── app.css           ← estilos globales (Tailwind)
├── routes/
│   └── web.php               ← todas las rutas (organizadas por rol)
├── tests/
│   ├── Feature/              ← tests activos (22 tests)
│   ├── Unit/                 ← tests unitarios
│   └── Legacy/               ← tests de versión Blade (no correr)
└── .env                      ← variables de entorno (NO se sube a git)
```

---

## ⚠️ Cosas importantes

### El `.env` nunca se commitea
Si vas a configurar en una PC nueva, copiá `.env.example` como `.env` y completá los valores reales (DB, mail, etc.).

### Los tests de `tests/Legacy/` no corren
Son de la versión anterior (Livewire/Blade). Están guardados como referencia histórica.
**Correr solo** `php artisan test` — ejecuta Feature/ y Unit/ automáticamente.

### El `public/build/` no se commitea
Vite genera los assets en `public/build/` al correr `npm run build`. Está en `.gitignore`.  
En producción siempre correr `npm run build` después de hacer pull.

---

## 🆘 Problemas frecuentes

| Problema | Solución |
|---|---|
| `npm install` falla con peer deps | Usar `npm install --legacy-peer-deps` |
| Página en blanco después de pull | Correr `npm run build` y `php artisan config:cache` |
| Error 419 (CSRF) | Correr `php artisan config:clear && php artisan cache:clear` |
| Migrations fallan | Revisar que la DB esté configurada en `.env` |
| `php artisan test` falla | Verificar que `.env.testing` apunta a SQLite o DB de testing |
