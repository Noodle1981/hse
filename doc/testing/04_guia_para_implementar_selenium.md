# 🧑‍💻 Guía Personal — Cómo Implementar Tests con Selenium

> Para consultar cuando llegue el momento de armar el proyecto de testing automatizado.

---

## ¿Qué pasarle al asistente de IA para cada test?

Cuando vayas a implementar un test específico, pasale estos 4 elementos:

### 1. La URL de la página
```
http://127.0.0.1:8000/gestor/trainings/create
```

### 2. Solo el HTML del formulario relevante
**No el HTML completo de la página**, solo el fragmento que involucra el test.  
Obtenerlo con: botón derecho → "Inspeccionar" → copiar el bloque del formulario.

Ejemplo de lo útil:
```html
<input type="email" id="email" wire:model="email">
<input type="password" id="password" wire:model="password">
<button type="submit">Iniciar Sesión</button>
```
Selenium va a usar esos `id`, `name`, textos de botones para ubicar los elementos.

### 3. El caso de test del archivo 02
Copiar el bloque del test que querés implementar del archivo
`doc/testing/02_prompt_selenium_testing.md`.

### 4. El contexto de roles (ya está en el archivo 02)
Las credenciales y roles del sistema ya están documentados, solo recordarle al
asistente cuáles son si no los tiene en contexto.

---

## ¿Qué NO pasarle?

| ❌ No hace falta | Por qué |
|---|---|
| Código PHP de Livewire | La lógica del servidor no le importa a Selenium |
| Migraciones o modelos | Selenium solo interactúa con el navegador |
| Todo el HTML completo | Solo el fragmento del formulario/componente relevante |
| El `web.php` completo | Solo la URL de la ruta específica |

---

## Atributos HTML más importantes para Selenium

Selenium necesita "agarrarse" de algo para encontrar un elemento.
En XamaSafe, buscar en este orden de prioridad:

| Prioridad | Atributo | Ejemplo Selenium |
|---|---|---|
| 1 | `id` | `driver.find_element("id", "email")` |
| 2 | `name` | `driver.find_element("name", "title")` |
| 3 | `wire:model` | Usar CSS: `[wire\\:model='title']` |
| 4 | Texto del botón | `driver.find_element("xpath", "//button[text()='Guardar']")` |
| 5 | Clase CSS única | `driver.find_element("css selector", ".btn-primary")` |

> [!TIP]
> En Livewire, los `wire:model` son los más confiables para inputs porque
> siempre están vinculados a la lógica del componente.

---

## Reglas de oro con Livewire

Livewire usa AJAX para actualizar la UI. Esto significa que después de cada
acción (click, input, submit), hay que **esperar** antes de continuar.

```python
# SIEMPRE hacer esto después de interactuar con un componente Livewire:
from helpers.wait_helpers import wait_for_livewire

driver.find_element("id", "title").send_keys("Mi Capacitación")
wait_for_livewire(driver)  # ← esperar que Livewire procese

driver.find_element("css selector", "button[type='submit']").click()
wait_for_livewire(driver)  # ← esperar antes de hacer assertions
```

**Sin este wait, los tests van a fallar intermitentemente ("flaky tests").**

---

## Estructura del test (plantilla base)

```python
def test_nombre_descriptivo(driver):
    # 1. ARRANGE — Preparar el estado inicial
    login(driver, role="gestor")
    wait_for_redirect(driver, "/gestor/dashboard")

    # 2. ACT — Ejecutar la acción a testear
    driver.get(f"{BASE_URL}/gestor/trainings/create")
    driver.find_element("id", "title").send_keys("Capacitación de Prueba")
    wait_for_livewire(driver)
    driver.find_element("css selector", "button[type='submit']").click()
    wait_for_livewire(driver)

    # 3. ASSERT — Verificar el resultado
    assert "/gestor/trainings" in driver.current_url
    assert "Capacitación de Prueba" in driver.page_source
```

---

## Orden para implementar los tests (de menor a mayor complejidad)

```
1. test_01_auth.py           ← Más simple, base para todos los demás
2. test_02_access_control.py ← Simple, solo navegar a URLs
3. test_04_checklists.py     ← Formularios sin Livewire reactivo complejo
4. test_03_visitas.py        ← Ver listas, sin flujo completo
5. test_05_hallazgos.py      ← Similar a visitas
6. test_06_pdfs.py           ← Verificar descargas
7. test_07_contrato_anual.py ← Livewire reactivo
8. test_08_escenarios_visita.py ← El más complejo, flujos end-to-end
```

---

## Checklist antes de correr los tests

- [ ] `php artisan serve` corriendo en `http://127.0.0.1:8000`
- [ ] `npm run dev` corriendo (para assets)
- [ ] DB con datos de prueba (`php artisan db:seed` o seeder de testing)
- [ ] Usuarios de prueba creados con credenciales del archivo `credenciales.md`
- [ ] ChromeDriver instalado y compatible con la versión del Chrome instalada
- [ ] Dependencias Python instaladas: `pip install -r requirements.txt`

---

## Comando para correr tests

```bash
# Todos los tests
pytest tests/ -v --html=report.html

# Un archivo específico
pytest tests/test_01_auth.py -v

# Un test específico
pytest tests/test_01_auth.py::test_login_gestor_exitoso -v

# Ver reporte visual
start report.html   # Windows
```

---

## Cuando un test falla

1. **Leer el mensaje de error** → generalmente dice qué elemento no encontró
2. **Revisar si cambió el HTML** → inspeccionar el elemento en el navegador
3. **Agregar más tiempo de espera** → aumentar el `timeout` del `wait_for_livewire`
4. **Verificar que los datos de prueba existen** en la DB
5. **Correr solo ese test** con `-v` para ver el detalle completo

> [!WARNING]
> Si un test pasa a veces y falla otras ("flaky"), casi siempre es un problema
> de timing con Livewire. Aumentar el `timeout` o agregar un `wait_for_livewire`
> adicional antes del assertion.
