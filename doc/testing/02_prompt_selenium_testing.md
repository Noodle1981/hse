# 🤖 Prompt para Testing Automatizado con Selenium — XamaSafe

> Copiá y pegá este prompt en tu asistente de IA del proyecto de Selenium.  
> Adaptá las credenciales y URL según tu entorno.

---

## PROMPT BASE (copiar completo)

```
Eres un experto en testing automatizado con Selenium y Python.
Voy a darte el contexto de una aplicación web Laravel que necesito testear.

## APLICACIÓN: XamaSafe v1.0

URL base: http://127.0.0.1:8000

### Roles del sistema
| Rol | Email de prueba | Password |
|---|---|---|
| super_admin | xamanen@app.com | password |
| Administrador (Gestor) | gestor@app.com | password |
| Técnico | tecnico@app.com | password |

### Módulos activos (MVP)
- Login / Logout
- Dashboard por rol (redirect automático)
- Gestión de Empresas y Sedes (/gestor/companies)
- Gestión de Usuarios y Técnicos (/gestor/users)
- Asignaciones de checklists (/gestor/assignments)
- Visitas técnicas (/gestor/visits/history y /tecnico/visits)
- Checklists dinámicos (/gestor/checklists)
- Capacitaciones (/gestor/trainings y /tecnico/trainings)
- Hallazgos (/gestor/hallazgos y /tecnico/mis-hallazgos)
- Incidentes (/gestor/incidentes y /tecnico/incidentes)
- Contrato Anual (/gestor/contrato-anual)
- Informe Maestro (/gestor/master-reports y /tecnico/master-reports)
- Bitácora del sistema (/administrativos/bitacora)

### Tecnologías
- Backend: Laravel 11 + Livewire 4 (componentes reactivos)
- Frontend: Alpine.js + Tailwind CSS
- IMPORTANTE: Livewire usa AJAX, por lo que debes esperar que los
  elementos carguen dinámicamente. Usar WebDriverWait siempre.

---

## ESTRUCTURA DEL PROYECTO SELENIUM

Crear la siguiente estructura de carpetas:

```
xamasafe_tests/
├── conftest.py              # Setup global: driver, login helper
├── pages/                   
│   ├── login_page.py        # Page Object: pantalla de login
│   ├── dashboard_page.py    # Page Object: dashboard por rol
│   ├── visit_page.py        # Page Object: gestión de visitas
│   ├── checklist_page.py    # Page Object: checklists
│   └── hallazgo_page.py     # Page Object: hallazgos
├── tests/
│   ├── test_01_auth.py          # Tests de autenticación
│   ├── test_02_access_control.py # Tests de control de roles
│   ├── test_03_visitas.py        # Tests de flujo de visitas
│   ├── test_04_checklists.py     # Tests de checklists
│   ├── test_05_hallazgos.py      # Tests de hallazgos
│   ├── test_06_pdfs.py           # Tests de generación de PDFs
│   └── test_07_contrato_anual.py # Tests del contrato anual
├── helpers/
│   └── wait_helpers.py      # Helpers para esperar elementos Livewire
└── requirements.txt
```

---

## CASOS DE PRUEBA A IMPLEMENTAR

### TEST 01 — Autenticación (`test_01_auth.py`)
```
test_login_gestor_exitoso:
  - Ir a /login
  - Ingresar credenciales de Administrador
  - Verificar redirect a /gestor/dashboard
  - Verificar que el nombre del usuario aparece en la pantalla

test_login_tecnico_exitoso:
  - Ir a /login
  - Ingresar credenciales de Técnico
  - Verificar redirect a /tecnico/dashboard

test_login_credenciales_incorrectas:
  - Ir a /login
  - Ingresar email/password incorrectos
  - Verificar que aparece mensaje de error
  - Verificar que NO hay redirect

test_logout:
  - Login como Gestor
  - Hacer click en logout
  - Verificar redirect a /login
  - Verificar que /gestor/dashboard redirige a /login
```

### TEST 02 — Control de Acceso por Roles (`test_02_access_control.py`)
```
test_tecnico_no_puede_acceder_a_gestor:
  - Login como Técnico
  - Intentar acceder a /gestor/dashboard directamente
  - Verificar que retorna 403 o redirige a login/dashboard del técnico

test_gestor_no_puede_acceder_a_admin:
  - Login como Administrador
  - Intentar acceder a /admin/dashboard
  - Verificar que retorna 403

test_usuario_no_autenticado_redirige_a_login:
  - Sin login, acceder a /gestor/dashboard
  - Verificar redirect a /login
```

### TEST 03 — Flujo de Visitas (`test_03_visitas.py`)
```
test_gestor_ve_historial_de_visitas:
  - Login como Gestor
  - Ir a /gestor/visits/history
  - Verificar que la página carga sin error
  - Verificar que existe tabla o mensaje de "sin visitas"

test_tecnico_ve_sus_visitas:
  - Login como Técnico
  - Ir a /tecnico/visits
  - Verificar que la página carga correctamente
  - Verificar que solo ve visitas propias (no de otros técnicos)
```

### TEST 04 — Checklists (`test_04_checklists.py`)
```
test_gestor_ve_lista_de_checklists:
  - Login como Gestor
  - Ir a /gestor/checklists
  - Verificar que la página carga
  - Verificar que existe botón "Nuevo Checklist"

test_crear_checklist_basico:
  - Login como Gestor
  - Ir a /gestor/checklists/create
  - Completar: título, descripción
  - Agregar pregunta tipo texto
  - Hacer click en "Guardar"
  - Verificar redirect a lista de checklists
  - Verificar que el nuevo checklist aparece en la lista
```

### TEST 05 — Hallazgos (`test_05_hallazgos.py`)
```
test_gestor_ve_todos_los_hallazgos:
  - Login como Gestor
  - Ir a /gestor/hallazgos
  - Verificar que la página carga

test_tecnico_ve_solo_sus_hallazgos:
  - Login como Técnico
  - Ir a /tecnico/mis-hallazgos
  - Verificar que la página carga
  - Verificar KPIs personales visibles

test_hallazgo_solo_desde_visita:
  - Login como Técnico
  - Verificar que NO existe botón de "Crear Hallazgo" fuera de una visita
```

### TEST 06 — Generación de PDFs (`test_06_pdfs.py`)
```
IMPORTANTE: Para PDFs, verificar que el endpoint responde con status 200
y Content-Type application/pdf. Usar requests o verificar la descarga.

test_pdf_contrato_anual:
  - Login como Gestor
  - Ir a /gestor/contrato-anual
  - Hacer click en "Exportar PDF"
  - Verificar que se inicia la descarga o que no hay error 500

test_pdf_certificado_visita:
  - Login como Gestor/Técnico
  - Navegar a una visita completada
  - Hacer click en "Descargar Constancia"
  - Verificar descarga exitosa
```

### TEST 07 — Contrato Anual (`test_07_contrato_anual.py`)
```
test_dashboard_contrato_anual_carga:
  - Login como Gestor
  - Ir a /gestor/contrato-anual
  - Verificar que la página carga sin error
  - Verificar que existe la tabla de planificación anual

test_marcar_servicio_en_mes:
  - Login como Gestor
  - Ir a /gestor/contrato-anual
  - Hacer click en una celda del calendario
  - Verificar que el estado cambia (Livewire reactivo)
  - Esperar con WebDriverWait a que el cambio se aplique
```

### TEST 08 — Escenarios de Visita (Casos de Negocio) (`test_08_escenarios_visita.py`)
```
NOTA: Estos tests requieren datos previos en la DB:
- Al menos 1 visita creada por el Gestor y asignada al Técnico
- Al menos 1 checklist activo disponible
- Al menos 1 capacitación programada para la sede

test_escenario_1_visita_con_todo_asignado:
  - Login como Gestor, crear visita asignando checklist + capacitación al técnico
  - Login como Técnico, ir a /tecnico/visits
  - Ingresar a la visita en progreso
  - Completar el checklist asignado (wait_for_livewire después de cada paso)
  - Ejecutar la capacitación asignada
  - Ir a finalizar: escribir observaciones (mín. 10 caracteres)
  - Ingresar nombre del responsable
  - Firmar en el canvas
  - Verificar redirect a lista de visitas
  - Verificar que la visita aparece como "completada"
  - Login como Gestor, verificar que la visita aparece en historial con actividades

test_escenario_2a_visita_autoasignacion_check_y_capacitacion:
  - Login como Gestor, crear visita SIN asignar nada
  - Login como Técnico, ingresar a la visita
  - Usar "Auto-asignarse checklist" y seleccionar uno disponible
  - Completar el checklist
  - Agregar capacitación y registrarla
  - Finalizar visita con observaciones y firma
  - Verificar PDF generado (no error 500)

test_escenario_2b_visita_solo_checklist:
  - Login como Técnico, ingresar a visita sin asignaciones
  - Auto-asignarse y completar solo 1 checklist
  - Sin agregar capacitación, hallazgo ni incidente
  - Finalizar con observaciones y firma
  - Verificar visita completada

test_escenario_2c_visita_solo_capacitacion:
  - Login como Técnico, ingresar a visita sin asignaciones
  - Registrar solo 1 capacitación (sin checklist)
  - Finalizar con observaciones y firma
  - Verificar visita completada

test_escenario_3_visita_con_hallazgo_e_incidente:
  - Login como Gestor, asignar 1 checklist al técnico
  - Login como Técnico, completar el checklist asignado
  - Auto-asignar 1 capacitación y ejecutarla
  - Reportar 1 hallazgo con foto y criticidad "Alto"
  - Reportar 1 incidente
  - Finalizar visita con observaciones y firma
  - Verificar que el hallazgo aparece en /tecnico/mis-hallazgos
  - Login como Gestor, verificar hallazgo en /gestor/hallazgos

test_escenario_4_visita_sin_actividades:
  - Login como Técnico, ingresar a visita sin asignaciones
  - NO completar checklist, capacitación, hallazgo ni incidente
  - Intentar finalizar SIN escribir observaciones → verificar error de validación
  - Escribir observación (mín. 10 caracteres)
  - Ingresar nombre del responsable y firmar
  - Verificar que la visita se completa correctamente
  - Verificar que el PDF se genera sin errores (no error 500)
```


---

## HELPERS PARA LIVEWIRE

Livewire usa AJAX, así que los elementos no cargan instantáneamente.
Usar siempre WebDriverWait:

```python
# helpers/wait_helpers.py

from selenium.webdriver.support.ui import WebDriverWait  
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By

def wait_for_element(driver, selector, timeout=10):
    return WebDriverWait(driver, timeout).until(
        EC.presence_of_element_located((By.CSS_SELECTOR, selector))
    )

def wait_for_livewire(driver, timeout=5):
    """Esperar a que Livewire termine de procesar"""
    import time
    time.sleep(0.5)  # Mínimo para que Livewire inicie la request
    WebDriverWait(driver, timeout).until(
        lambda d: d.execute_script(
            "return typeof window.Livewire === 'undefined' || "
            "window.Livewire.all().every(c => !c.__livewire_request_in_progress)"
        )
    )

def wait_for_redirect(driver, expected_url_fragment, timeout=10):
    WebDriverWait(driver, timeout).until(
        EC.url_contains(expected_url_fragment)
    )
```

---

## CONFIGURACIÓN (`conftest.py`)

```python
import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options

BASE_URL = "http://127.0.0.1:8000"

USERS = {
    "gestor": {"email": "gestor@app.com", "password": "password"},
    "tecnico": {"email": "tecnico@app.com", "password": "password"},
    "super_admin": {"email": "xamanen@app.com", "password": "password"},
}

@pytest.fixture(scope="session")
def driver():
    options = Options()
    # options.add_argument("--headless")  # Descomentar para CI/CD
    options.add_argument("--window-size=1920,1080")
    driver = webdriver.Chrome(options=options)
    driver.implicitly_wait(5)
    yield driver
    driver.quit()

def login(driver, role="gestor"):
    user = USERS[role]
    driver.get(f"{BASE_URL}/login")
    driver.find_element("id", "email").send_keys(user["email"])
    driver.find_element("id", "password").send_keys(user["password"])
    driver.find_element("css selector", "button[type='submit']").click()
```

---

## REQUIREMENTS.TXT

```
selenium>=4.18.0
pytest>=8.0.0
pytest-html>=4.1.0
webdriver-manager>=4.0.0
```

Instalar con:
```bash
pip install -r requirements.txt
```

Correr todos los tests:
```bash
pytest tests/ -v --html=report.html
```

Correr un módulo específico:
```bash
pytest tests/test_01_auth.py -v
```
```

---

## Notas Importantes

> [!WARNING]
> Los tests de Selenium requieren que el servidor Laravel esté corriendo
> (`php artisan serve`) y que la base de datos tenga datos de prueba.
> Usar un seeder de testing separado para no mezclar con datos reales.

> [!TIP]
> Empezar con los tests de autenticación y control de acceso, son los más
> fáciles de implementar y los más críticos para producción.

> [!NOTE]
> Para el Contrato Anual y componentes Livewire reactivos, siempre usar
> `wait_for_livewire()` después de cada acción (click, input) para esperar
> que la request AJAX se complete antes de hacer assertions.
