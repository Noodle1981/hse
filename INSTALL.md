# 🚀 Guía de Instalación - EP-Back V1.2.0.0

## 📋 Requisitos del Sistema

### Software Necesario

| Software | Versión Mínima | Recomendado |
|----------|----------------|-------------|
| PHP | 8.2 | 8.3 |
| Composer | 2.x | latest |
| Node.js | 18.x | 20.x |
| NPM | 9.x | 10.x |

### Extensiones PHP Requeridas

```
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- SQLite3
```

---

## ⚡ Instalación Rápida

### Opción A: Con Herd (Recomendado)

**Herd** incluye PHP, Composer y todo lo necesario preconfigurado.

```bash
# 1. Instalar Herd
# Windows: https://herd.laravel.com/windows
# Mac: https://herd.laravel.com

# 2. Clonar en carpeta Herd
cd C:\Users\{usuario}\Herd
git clone https://github.com/tu-usuario/ep-back.git
cd ep-back

# 3. Instalar dependencias
composer install
npm install

# 4. Configurar
cp .env.example .env
php artisan key:generate
touch database/database.sqlite

# 5. Base de datos
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoChecklistsSeeder

# 6. Iniciar
php artisan serve
```

Accede a: **http://localhost:8000/login**

---

### Opción B: Instalación Manual

#### 1. Requisitos Previos

```bash
# Verificar PHP
php -v
# Debe mostrar: PHP 8.2.x o superior

# Verificar Composer
composer -V

# Verificar Node
node -v
npm -v
```

#### 2. Clonar Repositorio

```bash
git clone https://github.com/tu-usuario/ep-back.git
cd ep-back
```

#### 3. Dependencias PHP

```bash
composer install

# Si hay problemas de memoria:
composer install --no-dev --optimize-autoloader
```

#### 4. Dependencias JavaScript

```bash
npm install

# Compilar assets (desarrollo)
npm run dev

# Compilar assets (producción)
npm run build
```

#### 5. Configuración

```bash
# Copiar .env
cp .env.example .env

# En Windows:
copy .env.example .env

# Generar APP_KEY
php artisan key:generate
```

#### 6. Base de Datos

**SQLite (Recomendado para desarrollo):**

```bash
# Crear archivo
touch database/database.sqlite

# En Windows PowerShell:
New-Item database/database.sqlite -ItemType File

# Configurar en .env
DB_CONNECTION=sqlite
```

**MySQL (Producción):**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ep_back
DB_USERNAME=root
DB_PASSWORD=tu_password
```

#### 7. Migraciones y Seeders

```bash
# Migrar base de datos
php artisan migrate

# O con fresh (elimina todo)
php artisan migrate:fresh

# Datos demo (RECOMENDADO)
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoChecklistsSeeder
```

#### 8. Iniciar Servidor

```bash
php artisan serve

# Servidor disponible en:
# http://127.0.0.1:8000
```

---

## 🔧 Configuración Detallada

### Archivo .env

```env
APP_NAME="EP Checklists"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de datos SQLite
DB_CONNECTION=sqlite

# Email (opcional, para notificaciones futuras)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@ep.com
MAIL_FROM_NAME="${APP_NAME}"

# Sanctum (API)
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

---

## 👥 Usuarios de Prueba

Después de ejecutar el seeder, tendrás:

### Administrador

```
Email: admin@ep.com
Contraseña: password

Permisos:
✅ Ver dashboard
✅ Crear/Editar checklists
✅ Llenar checklists
✅ Ver historial
```

### Técnicos (5)

```
Email: tecnico1@ep.com (hasta tecnico5)
Contraseña: password

Permisos:
✅ Ver dashboard
❌ Crear/Editar checklists
✅ Llenar checklists
✅ Ver historial
```

---

## 📊 Datos Demo

El seeder crea:

- **8 checklists** diferentes
- **102 inspecciones** (últimos 90 días)
- **6 usuarios** (1 admin + 5 técnicos)

```bash
# Regenerar datos
php artisan migrate:fresh --seed
php artisan db:seed --class=DemoChecklistsSeeder
```

---

## 🧪 Verificación

### Tests

```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=DynamicChecklist

# Con coverage (requiere Xdebug)
php artisan test --coverage
```

Resultado esperado: **26 tests, 57 assertions ✅**

### Rutas

```bash
# Listar todas las rutas
php artisan route:list

# Filtrar rutas de checklists
php artisan route:list --path=checklists
```

---

## 🚨 Troubleshooting

### Error: "SQLSTATE[HY000]: General error: 14 unable to open database file"

```bash
# Crear carpeta database si no existe
mkdir database

# Crear archivo SQLite
touch database/database.sqlite

# Dar permisos (Linux/Mac)
chmod 777 database
chmod 666 database/database.sqlite
```

### Error: "Class 'PDO' not found"

```bash
# Instalar extensión SQLite para PHP
# Ubuntu:
sudo apt-get install php8.2-sqlite3

# Mac (Homebrew):
brew install php@8.2
```

### Error: "npm run dev" no funciona

```bash
# Limpiar cache
npm cache clean --force

# Reinstalar
rm -rf node_modules package-lock.json
npm install
```

### Error: memoria insuficiente al hacer `composer install`

```bash
# Aumentar límite de memoria temporalmente
php -d memory_limit=-1 /usr/local/bin/composer install
```

---

## 🔄 Actualización

```bash
# 1. Backup de base de datos
cp database/database.sqlite database/database.backup.sqlite

# 2. Pull cambios
git pull origin main

# 3. Actualizar dependencias
composer install
npm install

# 4. Ejecutar migraciones
php artisan migrate

# 5. Limpiar cache
php artisan optimize:clear
```

---

## 🌐 Deploy a Producción

### Preparación

```bash
# 1. Compilar assets
npm run build

# 2. Optimizar autoload
composer install --optimize-autoloader --no-dev

# 3. Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Cambiar .env
APP_ENV=production
APP_DEBUG=false
```

### Servidor Web

**Nginx:**

```nginx
server {
    listen 80;
    server_name ep-back.com;
    root /var/www/ep-back/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**Apache:**

Usar el `.htaccess` incluido en `/public`.

---

## 📞 Soporte

Si encuentras problemas:

1. Verifica los [logs](storage/logs/laravel.log)
2. Revisa la [documentación](doc/)
3. Contacta a soporte: soporte@ep.com

---

*EP-Back V1.2.0.0 - Guía de Instalación*
