# Plan de Implementación Futura: Consentimiento de Auditoría y Términos de Servicio (XamaSafe)

Este documento detalla el plan técnico para incorporar una pantalla/modal bloqueante de aceptación de términos de servicio y consentimiento de auditoría horaria/GPS para los usuarios de XamaSafe en su primer inicio de sesión.

---

## 1. Cambios en Base de Datos (Backend)

### A. Migración de Laravel
Crear una migración para agregar el registro de aceptación en la tabla de usuarios:
`php artisan make:migration add_accepted_terms_at_to_users_table`

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('accepted_terms_at')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('accepted_terms_at');
        });
    }
};
```

---

## 2. Middleware de Control (Backend)

Crear un middleware en Laravel para interceptar las peticiones y verificar si el usuario logueado ha aceptado los términos.

### A. Comando de creación
`php artisan make:middleware EnsureTermsAreAccepted`

### B. Lógica del Middleware (`EnsureTermsAreAccepted.php`)
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTermsAreAccepted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Evitar bucle infinito de redirecciones
        if ($user && !$user->accepted_terms_at && !$request->routeIs('terms.show', 'terms.accept', 'logout')) {
            return redirect()->route('terms.show');
        }

        return $next($next);
    }
}
```

### C. Registro en `bootstrap/app.php`
Registrar el middleware en el grupo de rutas `web`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\EnsureTermsAreAccepted::class,
    ]);
})
```

---

## 3. Endpoints & Rutas (`routes/web.php`)

```php
use App\Http\Controllers\TermsController;

Route::middleware(['auth'])->group(function () {
    Route::get('/terms-and-conditions', [TermsController::class, 'show'])->name('terms.show');
    Route::post('/terms-and-conditions/accept', [TermsController::class, 'accept'])->name('terms.accept');
});
```

---

## 4. Controlador de Términos (`TermsController.php`)

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TermsController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/TermsAndConditions', [
            'user' => Auth::user(),
        ]);
    }

    public function accept(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'accepted_terms_at' => now(),
        ]);

        // Redirigir al dashboard según el rol
        $fallbackRoute = ['gestor', 'super_admin', 'administrador', 'admin'].includes($user->rol)
            ? 'gestor.checklists.index'
            : 'tecnico.checklists.tasks.index';

        return redirect()->route($fallbackRoute)->with('flash', [
            'success' => 'Términos y condiciones aceptados correctamente.'
        ]);
    }
}
```

---

## 5. Diseño de Interfaz de Usuario (Frontend React + Inertia)

Crear la vista en `resources/js/Pages/Auth/TermsAndConditions.jsx` utilizando la estética visual premium oscura (Dark) de XamaSafe.

### Puntos clave del Copy según Rol:

#### 🟠 Para Técnicos:
* **Firma Digital Inmutable:** Acepta que la firma registrada en el perfil tiene plena validez legal para certificar reportes.
* **Geolocalización en Campo (GPS):** Autoriza el registro de coordenadas geográficas al iniciar y cerrar visitas para la verificación de su presencia en planta (Proof of Presence).
* **Seguridad de Cuenta:** Se compromete al uso individual de su cuenta (estrictamente personal e intransferible).

#### 🔵 Para Gestores:
* **Uso Responsable de Licencias:** Responsabilidad sobre la creación y distribución de accesos a técnicos.
* **Integridad de Auditoría:** Compromiso de no compartir credenciales para no invalidar el estatus legal de los PDFs emitidos.
* **Política de Privacidad y Datos (GDPR/Compliance):** Tratamiento seguro de los reportes generados.

---

## 6. Verificación de Funcionamiento

### Pruebas Automatizadas
1. **Redirección:** Un usuario nuevo intenta acceder a `/tecnico/tareas` y es redirigido a `/terms-and-conditions`.
2. **Aceptación:** Al hacer POST a `/terms-and-conditions/accept`, la columna `accepted_terms_at` se completa con el timestamp actual y el usuario puede navegar libremente.
3. **Persistencia:** Al iniciar sesión en otro dispositivo, no se le vuelve a pedir la aceptación.
