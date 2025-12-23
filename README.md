# Equipo Preventivo - Sistema de Gestión Documental

Aplicación de gestión documental para la consultora de higiene y seguridad **Equipo Preventivo**. Sistema integral que permite gestionar checklists de seguridad, constancias de visita, informes técnicos y capacitaciones en materia de higiene y seguridad laboral.

## Características

- **Checklists**: Múltiples tipos de listas de verificación para auditorías y controles de seguridad
- **Constancias de Visita**: Generación automatizada de documentos de visita
- **Informes**: Creación y gestión de informes técnicos
- **Capacitaciones**: Registro y seguimiento de capacitaciones presenciales y virtuales
- **Gestión de Registros**: Base de datos centralizada de empresas, sedes y usuarios

## Requisitos Previos

Antes de ejecutar la aplicación, asegúrate de contar con lo siguiente:

- **PHP 8.1 o superior**
- **Composer** (gestor de dependencias de PHP)
- **Node.js** y **npm** (para las dependencias frontend)
- **SQLite** o **MySQL** (base de datos)

Se recomienda instalar **Herd**, que incluye PHP, Composer y otras herramientas necesarias:

- [Descargar Herd para Windows](https://herd.laravel.com/windows)
- [Descargar Herd para macOS](https://herd.laravel.com)

## Pasos de Instalación

1. **Clonar el repositorio** dentro de la carpeta Herd:
```bash
cd C:\Users\{usuario}\Herd
git clone https://github.com/carlostaglio/ep-back.git
cd ep-back
```

2. **Instalar dependencias de PHP**:
```bash
composer install
```

3. **Instalar dependencias de Node (frontend)**:
```bash
npm install
```

4. **Configurar archivo de entorno** - Copiar y renombrar:
```bash
copy .env.example .env
```

5. **Configurar la base de datos y las credenciales de email** en el archivo `.env`:
   - Para SQLite (recomendado para desarrollo):
   ```
   DB_CONNECTION=sqlite
   ```
   - Para MySQL, actualiza con tus credenciales:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ep_base
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   - Para el correo de gestión interna de la plataforma:
   ```
   MAIL_MAILER=log
   MAIL_SCHEME=null
   MAIL_HOST=127.0.0.1
   MAIL_PORT=2525
   MAIL_USERNAME=null
   MAIL_PASSWORD=null
   MAIL_FROM_ADDRESS="hello@example.com"
   MAIL_FROM_NAME="${APP_NAME}"
   ```
   - Para el correo de contacto de la landing:
   ```
   MAIL2_MAILER=log
   MAIL2_SCHEME=null
   MAIL2_HOST=127.0.0.1
   MAIL2_PORT=2525
   MAIL2_USERNAME=null
   MAIL2_PASSWORD=null
   MAIL2_FROM_ADDRESS="hello@example.com"
   MAIL2_FROM_NAME="${APP_NAME}"
   ```

6. **Generar clave de aplicación** y **ejecutar migraciones**:
```bash
php artisan key:generate
php artisan migrate
```

## Ejecución de la Aplicación

1. **Iniciar el servidor Laravel**:
```bash
php artisan serve
```

2. **En otra consola, compilar los assets frontend**:
```bash
npm run dev
```

3. **Acceder a la aplicación**:
- Aplicación: `http://127.0.0.1:8000/`
- API: `http://127.0.0.1:8000/api/`

Para compilar para producción:
```bash
npm run build
```

## Recursos Útiles

- [Documentación de Laravel](https://laravel.com/docs)
- [Documentación de Herd](https://herd.laravel.com)
- [API REST Best Practices](https://restfulapi.net/)

**© 2025 Equipo Preventivo - Sistema de Gestión Documental**
