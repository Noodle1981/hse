# 🛡️ Guía Anti-Entropía - EP-Back

## ¿Qué es la Entropía de Software?

La **entropía de software** es la tendencia natural del código a degradarse con el tiempo. Como la segunda ley de la termodinámica, el desorden tiende a aumentar si no se combate activamente.

### Síntomas de Alta Entropía:
- ❌ Código difícil de entender
- ❌ Bugs recurrentes en las mismas áreas
- ❌ Miedo a tocar ciertas partes del código
- ❌ Tiempo de desarrollo cada vez más lento
- ❌ Documentación desactualizada
- ❌ Tests que fallan aleatoriamente
- ❌ Dependencias obsoletas

---

## 🎯 Principios Fundamentales

### 1. La Regla del Boy Scout
> "Deja el código más limpio de como lo encontraste"

**Aplicación práctica:**
```php
// ❌ ANTES (encontrado)
public function getData() {
    $data = DB::table('users')->get();
    return $data;
}

// ✅ DESPUÉS (mejorado)
public function getActiveUsers(): Collection
{
    return User::query()
        ->where('active', true)
        ->orderBy('name')
        ->get();
}
```

### 2. YAGNI (You Aren't Gonna Need It)
> "No agregues funcionalidad hasta que sea necesaria"

**Aplicación:**
- ❌ No crear métodos "por si acaso"
- ❌ No agregar campos "que podrían servir"
- ✅ Implementar solo lo que el roadmap requiere
- ✅ Refactorizar cuando aparece el patrón real

### 3. DRY (Don't Repeat Yourself)
> "Cada pieza de conocimiento debe tener una representación única"

**Aplicación en EP-Back:**
```php
// ❌ MALO - Repetición
// En HallazgoManager
$hallazgos = Hallazgo::where('estado', 'pendiente')->get();

// En HallazgoDetail
$hallazgos = Hallazgo::where('estado', 'pendiente')->get();

// ✅ BUENO - Scope reutilizable
// En Hallazgo.php
public function scopePendientes($query) {
    return $query->where('estado', 'pendiente');
}

// Uso
Hallazgo::pendientes()->get();
```

---

## 📋 Checklist Anti-Entropía Semanal

### Lunes - Revisión de Código
- [ ] Revisar PRs pendientes
- [ ] Verificar que sigan las convenciones
- [ ] Buscar código duplicado
- [ ] Validar que haya tests

### Miércoles - Mantenimiento
- [ ] Ejecutar `composer outdated`
- [ ] Revisar logs de errores
- [ ] Verificar tests (todos en verde)
- [ ] Limpiar código comentado

### Viernes - Documentación
- [ ] Actualizar README si hubo cambios
- [ ] Documentar decisiones importantes
- [ ] Revisar que los comentarios sean actuales
- [ ] Actualizar ROADMAP.md

---

## 🏗️ Arquitectura Anti-Entropía

### 1. Separación de Responsabilidades

**Estructura Clara:**
```
app/
├── Models/              # Solo lógica de datos
├── Livewire/           # Solo lógica de UI
├── Services/           # Lógica de negocio compleja
├── Repositories/       # Acceso a datos (si es necesario)
└── Helpers/            # Funciones utilitarias
```

**Ejemplo - Extraer Lógica Compleja:**
```php
// ❌ MALO - Todo en el componente
class HallazgoManager extends Component
{
    public function calcularEstadisticas()
    {
        $total = Hallazgo::count();
        $pendientes = Hallazgo::where('estado', 'pendiente')->count();
        $criticos = Hallazgo::where('criticidad', 'critico')
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->count();
        // ... 50 líneas más
    }
}

// ✅ BUENO - Service dedicado
class HallazgoStatsService
{
    public function getKPIs(): array
    {
        return [
            'total' => $this->getTotalCount(),
            'pendientes' => $this->getPendientesCount(),
            'criticos' => $this->getCriticosCount(),
        ];
    }
    
    private function getTotalCount(): int
    {
        return Cache::remember('hallazgos.total', 300, fn() => 
            Hallazgo::count()
        );
    }
}
```

### 2. Convenciones de Nombres

**Consistencia es clave:**
```php
// ✅ BUENO - Nombres descriptivos y consistentes
public function getActiveHallazgosBySede(int $sedeId): Collection
public function markHallazgoAsResolved(Hallazgo $hallazgo): bool
public function sendVencimientoNotification(Hallazgo $hallazgo): void

// ❌ MALO - Nombres ambiguos
public function get($id)
public function do($h)
public function process()
```

### 3. Comentarios Útiles

```php
// ❌ MALO - Comentario obvio
// Obtiene el usuario
$user = User::find($id);

// ❌ MALO - Comentario desactualizado
// TODO: Implementar validación (ya está implementado)

// ✅ BUENO - Explica el "por qué"
// Usamos eager loading para evitar N+1 queries
// ya que cada hallazgo puede tener múltiples seguimientos
$hallazgos = Hallazgo::with(['seguimientos', 'fotos'])->get();

// ✅ BUENO - Documenta decisión de diseño
/**
 * No validamos el plazo_correccion aquí porque puede ser null
 * para hallazgos de baja criticidad según ROADMAP_HALLAZGOS.md
 */
public function store(Request $request)
```

---

## 🧪 Testing Anti-Entropía

### Regla de Oro
> "Si no tiene test, está roto (o lo estará pronto)"

**Cobertura Mínima:**
```php
// tests/Feature/HallazgoTest.php
class HallazgoTest extends TestCase
{
    /** @test */
    public function admin_puede_ver_todos_los_hallazgos()
    {
        $admin = User::factory()->admin()->create();
        $hallazgos = Hallazgo::factory()->count(5)->create();
        
        $this->actingAs($admin)
            ->get(route('hallazgos.index'))
            ->assertOk()
            ->assertSee($hallazgos->first()->titulo);
    }
    
    /** @test */
    public function tecnico_solo_ve_sus_hallazgos()
    {
        $tecnico = User::factory()->tecnico()->create();
        $suHallazgo = Hallazgo::factory()->create(['user_id' => $tecnico->id]);
        $otroHallazgo = Hallazgo::factory()->create();
        
        $this->actingAs($tecnico)
            ->get(route('mis-hallazgos'))
            ->assertSee($suHallazgo->titulo)
            ->assertDontSee($otroHallazgo->titulo);
    }
}
```

---

## 📦 Gestión de Dependencias

### Actualización Regular

**Comando semanal:**
```bash
# Ver dependencias desactualizadas
composer outdated

# Actualizar dependencias menores (seguras)
composer update --with-dependencies

# Verificar que todo funcione
php artisan test
```

**Política de Versiones:**
```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",  // ✅ Permite parches
        "livewire/livewire": "^3.0",   // ✅ Permite menores
        "specific-package": "2.5.1"    // ❌ Evitar versiones fijas
    }
}
```

---

## 🗑️ Limpieza de Código

### Código Muerto

**Buscar y eliminar:**
```bash
# Buscar TODOs viejos
grep -r "TODO" app/

# Buscar código comentado
grep -r "^[[:space:]]*\/\/" app/ | grep -v "\/\/"

# Buscar imports no usados (con herramientas)
composer require --dev nunomaduro/phpinsights
php artisan insights
```

**Ejemplo de limpieza:**
```php
// ❌ ANTES
class HallazgoManager extends Component
{
    // public $oldFilter = ''; // Ya no se usa
    // public function oldMethod() { ... } // Deprecado
    
    public $search = '';
    
    // TODO: Implementar filtro avanzado (hace 6 meses)
}

// ✅ DESPUÉS
class HallazgoManager extends Component
{
    public $search = '';
}
```

---

## 📚 Documentación Viva

### README.md Actualizado

**Estructura recomendada:**
```markdown
# EP-Back

## Instalación
[Pasos actualizados]

## Módulos Activos
- ✅ Checklists Dinámicos
- ✅ Matriz de Riesgos
- ✅ Hallazgos y Seguimiento (Fase 3 completa)
- 🚧 Notificaciones (En desarrollo)

## Comandos Útiles
[Lista de comandos comunes]

## Última Actualización
2025-12-30 - Fase 3 Hallazgos completada
```

### Documentar Decisiones (ADR - Architecture Decision Records)

**Crear archivo para decisiones importantes:**
```markdown
# doc/decisions/001-usar-livewire-para-hallazgos.md

## Contexto
Necesitamos UI interactiva para gestión de hallazgos

## Decisión
Usar Livewire 3 en lugar de Vue/React

## Consecuencias
+ Consistencia con el resto del proyecto
+ Menos complejidad (no API separada)
+ Más rápido de desarrollar
- Menos flexible para SPA futura
```

---

## 🔄 Refactoring Continuo

### Cuándo Refactorizar

**Señales de alerta:**
1. **Método > 50 líneas** → Dividir en métodos más pequeños
2. **Clase > 300 líneas** → Extraer responsabilidades
3. **Parámetros > 4** → Usar objetos de datos
4. **Condicionales anidados > 3 niveles** → Usar early returns
5. **Código duplicado > 2 veces** → Extraer a método/clase

**Ejemplo de refactoring:**
```php
// ❌ ANTES - Método largo con múltiples responsabilidades
public function save()
{
    $this->validate();
    
    if ($this->hallazgoId) {
        $hallazgo = Hallazgo::find($this->hallazgoId);
        $hallazgo->update([...]);
    } else {
        $hallazgo = Hallazgo::create([...]);
    }
    
    if ($this->fotos) {
        foreach ($this->fotos as $foto) {
            $path = $foto->store('hallazgos');
            HallazgoFoto::create([...]);
        }
    }
    
    if ($this->origen_type) {
        // vincular origen
    }
    
    session()->flash('message', 'Guardado');
    return redirect()->route('hallazgos.show', $hallazgo);
}

// ✅ DESPUÉS - Métodos pequeños y enfocados
public function save()
{
    $this->validate();
    
    $hallazgo = $this->saveHallazgo();
    $this->saveFotos($hallazgo);
    $this->linkOrigen($hallazgo);
    
    return $this->redirectWithSuccess($hallazgo);
}

private function saveHallazgo(): Hallazgo
{
    return $this->hallazgoId 
        ? $this->updateExisting()
        : $this->createNew();
}

private function saveFotos(Hallazgo $hallazgo): void
{
    if (!$this->fotos) return;
    
    collect($this->fotos)->each(
        fn($foto) => $this->storeFoto($foto, $hallazgo)
    );
}
```

---

## 🚨 Code Smells a Evitar

### 1. God Class (Clase Dios)
```php
// ❌ MALO - Hace demasiado
class HallazgoManager extends Component
{
    // 50 propiedades públicas
    // 100 métodos
    // 2000 líneas
}

// ✅ BUENO - Responsabilidad única
class HallazgoManager extends Component { /* solo UI */ }
class HallazgoService { /* solo lógica de negocio */ }
class HallazgoRepository { /* solo acceso a datos */ }
```

### 2. Magic Numbers
```php
// ❌ MALO
if ($hallazgo->dias_vencido > 30) { ... }

// ✅ BUENO
const DIAS_CRITICO = 30;
if ($hallazgo->dias_vencido > self::DIAS_CRITICO) { ... }
```

### 3. Primitive Obsession
```php
// ❌ MALO
public function createHallazgo(
    string $titulo,
    string $descripcion,
    string $criticidad,
    string $estado,
    // ... 10 parámetros más
) { }

// ✅ BUENO
class HallazgoData
{
    public function __construct(
        public string $titulo,
        public string $descripcion,
        public Criticidad $criticidad,
        public Estado $estado,
    ) {}
}

public function createHallazgo(HallazgoData $data) { }
```

---

## 🛠️ Herramientas Recomendadas

### 1. Laravel Pint (Code Style)
```bash
composer require laravel/pint --dev
./vendor/bin/pint
```

### 2. PHPStan (Análisis Estático)
```bash
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse
```

### 3. PHP Insights (Calidad de Código)
```bash
composer require --dev nunomaduro/phpinsights
php artisan insights
```

### 4. Laravel Debugbar (Desarrollo)
```bash
composer require --dev barryvdh/laravel-debugbar
```

---

## 📊 Métricas de Salud del Código

### KPIs a Monitorear

1. **Cobertura de Tests**: > 70%
2. **Complejidad Ciclomática**: < 10 por método
3. **Duplicación de Código**: < 3%
4. **Deuda Técnica**: < 5% del tiempo total
5. **Dependencias Desactualizadas**: 0 críticas

### Comando de Salud
```bash
# Crear script de salud
php artisan make:command HealthCheck

# Ejecutar
php artisan health:check
```

---

## 🎓 Mejores Prácticas EP-Back

### 1. Estructura de Componentes Livewire
```php
class HallazgoManager extends Component
{
    // 1. Traits
    use WithPagination;
    
    // 2. Propiedades públicas (estado)
    public string $search = '';
    
    // 3. Propiedades protegidas (config)
    protected $queryString = ['search'];
    
    // 4. Métodos de ciclo de vida
    public function mount() { }
    
    // 5. Métodos de acción
    public function delete($id) { }
    
    // 6. Métodos privados (helpers)
    private function buildQuery() { }
    
    // 7. Render (siempre al final)
    public function render() { }
}
```

### 2. Modelos Eloquent
```php
class Hallazgo extends Model
{
    // 1. Configuración
    protected $table = 'hallazgos';
    protected $fillable = [...];
    protected $casts = [...];
    
    // 2. Constantes
    const ESTADO_PENDIENTE = 'pendiente';
    
    // 3. Relaciones
    public function sede() { }
    
    // 4. Scopes
    public function scopePendientes($query) { }
    
    // 5. Accessors/Mutators
    public function getTituloFormateadoAttribute() { }
    
    // 6. Métodos de negocio
    public function estaVencido(): bool { }
}
```

### 3. Migraciones
```php
// ✅ BUENO - Descriptivo y reversible
public function up()
{
    Schema::create('hallazgos', function (Blueprint $table) {
        $table->id();
        $table->string('titulo');
        // ...
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('hallazgos');
}
```

---

## 🔐 Seguridad Anti-Entropía

### Validación Consistente
```php
// ✅ Usar Form Requests
class StoreHallazgoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'criticidad' => 'required|in:bajo,medio,alto,critico',
        ];
    }
}

// Uso en componente
public function save()
{
    $validated = $this->validate(
        (new StoreHallazgoRequest())->rules()
    );
}
```

---

## 📅 Plan de Mantenimiento Mensual

### Semana 1: Auditoría
- Revisar código nuevo del mes
- Identificar deuda técnica
- Priorizar refactorings

### Semana 2: Actualización
- Actualizar dependencias
- Revisar vulnerabilidades
- Ejecutar tests completos

### Semana 3: Limpieza
- Eliminar código muerto
- Actualizar documentación
- Revisar TODOs

### Semana 4: Mejora
- Implementar 1-2 refactorings
- Agregar tests faltantes
- Optimizar queries lentas

---

## ✅ Checklist de Pull Request

Antes de hacer merge, verificar:

- [ ] Código sigue convenciones del proyecto
- [ ] No hay código comentado innecesario
- [ ] Nombres de variables/métodos son descriptivos
- [ ] Hay tests para nueva funcionalidad
- [ ] Documentación actualizada si es necesario
- [ ] No hay dependencias nuevas sin justificación
- [ ] Sin warnings de PHPStan/Pint
- [ ] Migraciones tienen rollback
- [ ] No hay secrets hardcodeados

---

## 🎯 Conclusión

La entropía es inevitable, pero **manejable**:

1. **Prevención** > Corrección
2. **Consistencia** > Perfección
3. **Documentación** > Memoria
4. **Automatización** > Disciplina manual
5. **Refactoring continuo** > Reescritura total

### Mantra Anti-Entropía
> "Código limpio no es el que no tiene bugs,  
> es el que es fácil de entender y modificar"

---

**Última actualización:** 2025-12-30  
**Versión:** 1.0  
**Mantenedor:** Equipo EP-Back
