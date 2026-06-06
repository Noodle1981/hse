---
description: implementacion de gps
---

# Plan de Implementación: Validación GPS (Proof of Presence)

## Objetivo
Capturar la ubicación GPS del técnico al momento de **Abrir** una visita (Entrada) y al **Cerrar/Finalizar** la visita (Salida), comparándola mediante geocercas con las coordenadas registradas de la Sede para garantizar que la inspección se realizó en el sitio correcto.

## 🌟 Valor Agregado para XamaSafe
1. **Auditoría Transparente:** La prueba criptográfica/digital de que el técnico estuvo en la sede incrementa la confianza de los clientes corporativos.
2. **Prevención de Fraude:** Evita las "inspecciones de escritorio" (llenar checklists desde la casa u oficina).
3. **Mapeo de Tiempos Reales:** Al cruzar Entradas/Salidas geolocalizadas, XamaSafe puede calcular tiempos de viaje vs. tiempos efectivos de trabajo en planta.

---

## FASE 1: Preparación del Modelo de Datos

1. **Tabla `sedes`:**
   Actualmente asumo que existe un campo de texto con la ubicación. Necesitamos asegurar la existencia de:
   - `latitud` (decimal)
   - `longitud` (decimal)
   - `radio_tolerancia_metros` (integer, por defecto 500m).

2. **Tabla `visit_certificates` (o equivalente a la Visita):**
   Agregar columnas para el tracking:
   - `checkin_lat` / `checkin_lng`
   - `checkin_time`
   - `checkin_distance_meters` (distancia calculada a la sede al abrir)
   - `checkout_lat` / `checkout_lng`
   - `checkout_time`
   - `checkout_distance_meters`
   - `gps_verified` (boolean: true si ambas marcas cayeron dentro del radio de tolerancia).

---

## FASE 2: Captura en Frontend (Livewire + JS)

El frontend debe solicitar permiso al navegador/teléfono para acceder a la ubicación usando el API nativa de geolocalización.

```javascript
// Lógica conceptual a inyectar en la vista blade del inicio de visita
navigator.geolocation.getCurrentPosition(
    (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        // Enviar a Livewire
        @this.call('registerCheckIn', lat, lng);
    }, 
    (error) => {
        alert("Debes permitir el acceso a tu ubicación para iniciar la visita.");
    }, 
    { enableHighAccuracy: true } // <- CRÍTICO para móviles
);
```

**Flujo UI:**
1. Botón "Iniciar Visita" → Llama al GPS.
2. Si falla o deniega → Bloquea inicio (o lo marca como "fuera de rango/manual" según decidas).
3. Misma lógica al pulsar "Cerrar/Finalizar Visita".

---

## FASE 3: Cálculo Back-End (Fórmula de Haversine)

En el servicio o controller, usaremos la fórmula de Haversine o el paquete geográfico de base de datos para calcular la distancia en línea recta entre el pin del técnico y el pin de la Sede.

```php
public function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    // Radio de la tierra en metros (6371000)
    // Retorna la distancia en metros
}
```

- Si `distance <= sede->radio_tolerancia_metros` → `Validación OK.`
- Si está fuera del radio → Registrar un *Warning* o requerir justificación ("¿Por qué inicias fuera de locación?").

---

## FASE 4: Visualización (El "Wow Factor" para el Gestor)

1. **Ficha de la Visita (PDF e Interfaz UI):**
   - Mostrar un mini mapa estático en el PDF final marcando el PIN de la sede y el PIN del técnico (entrada/salida).
   - Un sello estilo `[ VERIFICADO POR GPS ]` en verde brillante en la cabecera.

2. **Dashboard de Gestores:**
   - Un KPI nuevo: `% de Visitas Verificadas por GPS`.

---

## Siguientes Pasos (Comandos a ejecutar en la próxima sesión)
Para iniciar, el próximo agente debe correr:
1. `php artisan make:migration add_gps_tracking_to_visits`
2. `php artisan make:migration add_coordinates_to_sedes`
3. Instalar/Configurar un helper de distancias geográficas.
